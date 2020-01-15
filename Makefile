help:
	echo "Liste des cibles : preparecs indocker et rmdocker"

preparecs:
	vendor/bin/phpcs --config-set installed_paths vendor/escapestudios/symfony2-coding-standard

inphp:
	docker-compose run --user="1000" php72 bash;

inapache:
	docker-compose run apache bash;

rmdocker:
	docker image rm personal-library_php72

update:
	docker-compose down && docker image rm personal-library_php72 && docker-compose up -d
