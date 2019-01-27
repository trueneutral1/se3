# Задание номер 3

Разработчика попросили получить данные от стороннего сервиса.
Данные необходимо кешировать. Ошибки необходимо логировать.
Он с задачей не справился :)
Надо показать ему, как правильно реализовывать данный функционал.

## Пример использования ##

```
$config = new DataLoaderConfig($host, $port, $user, $password);

$dataLoader = new DataLoader($config, $cacheTime);

$dataLoader->load(CacheItemPoolInterface $cache, LoggerInterface $logger);
```