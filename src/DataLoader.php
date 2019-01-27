<?php

namespace se3;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use se3\exceptions\BaseDataLoaderException;
use se3\exceptions\CacheLockException;

/**
 * Class DataLoader
 * @package se3
 */
class DataLoader
{
    private $config, $cacheTime;
    const WAIT_BETWEEN_RETRIES = 200;
    const NUM_RETRIES = 5;

    /**
     * DataLoader constructor.
     * @param DataLoaderConfig $config
     * @param int $cacheTime
     */
    public function __construct(DataLoaderConfig $config, int $cacheTime = 0)
    {
        $this->config = $config;
        $this->cacheTime = $cacheTime;
    }

    /**
     * @param array $request
     * @param CacheItemPoolInterface $cache
     * @param LoggerInterface $logger
     * @return mixed
     */
    public function load(array $request, CacheItemPoolInterface $cache, LoggerInterface $logger)
    {
        try {
            $cacheKey = $this->generateCacheKey($request);
            $lockKey = $cacheKey . '_lock';

            // осознанное решение, если ключ обновляется, лучше подождать, но выдать пользователю актуальные данные
            $this->waitForKeyAvailability($cache, $lockKey);

            $cacheItem = $cache->getItem($cacheKey);
            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }

            $this->lockKey($cache, $lockKey);

            $result = $this->makeRequest($request);

            if ($this->cacheTime) {

                $cacheItem->set($result)->expiresAfter($this->cacheTime);
            }

            $this->releaseKey($cache, $lockKey);

            return $result;

        } catch (BaseDataLoaderException $e) {
            $logger->log('critical', $e->getMessage());
        }
    }

    /**
     * @param array $request
     * @return array
     */
    private function makeRequest(array $request)
    {
        $result = array();

        //делаем запрос, кладем в $result

        return $result;
    }

    /**
     * @param array $request
     * @return string
     */
    private function generateCacheKey(array $request)
    {
        return md5(json_encode(ksort($request)));

    }

    /**
     * @param CacheItemPoolInterface $cache
     * @param string $lockKey
     * @throws CacheLockException
     */
    private function waitForKeyAvailability(CacheItemPoolInterface $cache, string $lockKey)
    {
        for ($counter = 0; $counter <= self::NUM_RETRIES; $counter++) {
            if (!$cache->getItem($lockKey)->isHit()) {
                return;
            }
            usleep(self::WAIT_BETWEEN_RETRIES);
        }
        throw new CacheLockException('cache lock', 1);
    }

    /**
     * @param CacheItemPoolInterface $cache
     * @param string $lockKey
     */
    private function lockKey(CacheItemPoolInterface $cache, string $lockKey)
    {
        $cache->getItem($lockKey)->set(true);
    }

    /**
     * @param CacheItemPoolInterface $cache
     * @param string $lockKey
     */
    private function releaseKey(CacheItemPoolInterface $cache, string $lockKey)
    {
        $cache->deleteItem($lockKey);
    }
}
