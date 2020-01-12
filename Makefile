indocker:
	docker-compose run --user="1000" php72 bash;

rmdocker:
	docker image rm personal-library_php7

