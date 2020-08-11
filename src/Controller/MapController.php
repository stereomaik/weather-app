<?php
/**
 * @author Michał Skrzypek <mcskrzypek@gmail.com>
 * @date date(2020-08-10)
 * @version 1.0
 */

namespace App\Controller;

use App\Entity\WeatherCheck;
use App\Repository\WeatherCheckRepository;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MapController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * MapController constructor.
     *
     * @param EntityManagerInterface $em Entity manager.
     * @param NormalizerInterface $normalizer Needed to convert entity to array.
     */
    public function __construct(EntityManagerInterface $em, NormalizerInterface $normalizer)
    {
        $this->em = $em;
        $this->normalizer = $normalizer;
    }

    /**
     * Homepage action method.
     *
     * @Route("/", name="map_homepage")
     * @return Response
     */
    public function index()
    {
        return $this->render('map/index.html.twig');
    }

    /**
     * History page action method.
     *
     * @Route("/history", name="map_history")
     * @return Response
     */
    public function history(Request $request, WeatherCheckRepository $repository, PaginatorInterface $paginator)
    {
        $checks = $paginator->paginate(
            $repository->getWeatherChecksQuery(),
            $request->query->getInt('page', 1),
            10
        );

        $temperatureStats = $repository->getTemperatureStats();
        $topCity = $repository->getTopSearchedCity();

        return $this->render('map/history.html.twig', compact('checks', 'temperatureStats', 'topCity'));
    }

    /**
     * Get weather data from Open Weather Maps
     *
     * @Route("/get-weather", name="map_get_weather", methods={"POST"})
     *
     * @param Request $request The request to get POST data from.
     * @param HttpClientInterface $client Needed to make the API call.
     *
     * @return JsonResponse
     * @throws ErrorException On possible tampering attempt or API non-200 status.
     * @throws ExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getWeather(Request $request, HttpClientInterface $client)
    {
        $requestData = json_decode($request->getContent(), true);
        $coordinates = $this->getValidatedLatitudeAndLongitude(
            $requestData['longitude'],
            $requestData['latitude']
        );

        if (!$coordinates) {
            throw new ErrorException("Longitude or latitude incorrect. Possible tampering attempt.");
        }

        $weatherResponse = $client->request(
            'GET',
            $this->getApiWeatherEndpoint($coordinates)
        );

        $statusCode = $weatherResponse->getStatusCode();
        if ($weatherResponse->getStatusCode() != '200') {
            throw new ErrorException("API Error with status code: " . $statusCode);
        }

        return new JsonResponse(
            $this->saveWeatherCheckAndReturnDataArray($weatherResponse->toArray())
        );
    }

    /**
     * Validates and returns coordinates.
     *
     * @param string|null $lon The longitude from request.
     * @param string|null $lat The latitude from request.
     *
     * @return array|null Coordinates on success, null on failure.
     */
    private function getValidatedLatitudeAndLongitude(?string $lon, ?string $lat): ?array
    {
        switch (true) {
            case !is_numeric($lat) || !is_numeric($lon):
                //no break
            case ((float)$lat > 90 || (float)$lat < -90):
                //no break
            case ((float)$lon > 180 || (float)$lon < -180):
                return null;
        }

        return [
            'longitude' => $lon,
            'latitude' => $lat
        ];
    }

    /**
     * Get complete Open Weather API endpoint URL.
     *
     * @param array $coordinates Latitude and longitude.
     *
     * @return string
     */
    private function getApiWeatherEndpoint(array $coordinates): string
    {
        return sprintf(
            $this->getParameter('open_weather_api'),
            $coordinates['latitude'],
            $coordinates['longitude'],
            $this->getParameter('open_weather_api_key')
        );
    }

    /**
     * Save the check in the DB and return array of saved data.
     *
     * @param array $weatherResponse Array of Open Weather Map API data.
     *
     * @return array
     * @throws ExceptionInterface
     */
    public function saveWeatherCheckAndReturnDataArray(array $weatherResponse): array
    {
        $weatherCheck = new WeatherCheck();
        $weatherCheck->setCity($weatherResponse['name'] ?: '(outside of administrative scope)')
            ->setLatitude($weatherResponse['coord']['lat'])
            ->setLongitude($weatherResponse['coord']['lon'])
            ->setTemperature($weatherResponse['main']['temp'])
            ->setClouds($weatherResponse['clouds']['all'])
            ->setWindSpeed($weatherResponse['wind']['speed'])
            ->setDescription($weatherResponse['weather'][0]['description']);

        $this->em->persist($weatherCheck);
        $this->em->flush();

        return $this->normalizer->normalize($weatherCheck);
    }
}