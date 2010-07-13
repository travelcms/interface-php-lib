mkdir coverage
phpunit --bootstrap bootstrap.php --log-junit coverage/log.xml --coverage-html=coverage AllTests