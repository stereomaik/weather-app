<?php
/**
 * @author Michał Skrzypek <mcskrzypek@gmail.com>
 * @date date(2020-08-10)
 * @version 1.0
 */

namespace App\Repository;

use App\Entity\WeatherCheck;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WeatherCheck|null find($id, $lockMode = null, $lockVersion = null)
 * @method WeatherCheck|null findOneBy(array $criteria, array $orderBy = null)
 * @method WeatherCheck[]    findAll()
 * @method WeatherCheck[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WeatherCheckRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WeatherCheck::class);
    }

    /**
     * Get all the weather checks query.
     *
     * @return Query
     */
    public function getWeatherChecksQuery(): Query
    {
        return $this->createQueryBuilder('weatherCheck')
            ->orderBy('weatherCheck.createdAt', 'DESC')
            ->getQuery();
    }

    /**
     * Get Min, Max and Average temperature.
     *
     * @return array|null
     * @throws NonUniqueResultException
     */
    public function getTemperatureStats(): ?array
    {
        return $this->createQueryBuilder('weatherCheck')
            ->select('AVG(weatherCheck.temperature) AS average')
            ->addSelect('MIN(weatherCheck.temperature) AS min')
            ->addSelect('MAX(weatherCheck.temperature) AS max')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get top searched city.
     *
     * @return array|null
     * @throws NonUniqueResultException
     */
    public function getTopSearchedCity(): ?array
    {
        return $this->createQueryBuilder('weatherCheck')
            ->select('weatherCheck.city')
            ->groupBy('weatherCheck.city')
            ->orderBy('COUNT(weatherCheck.createdAt)', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}
