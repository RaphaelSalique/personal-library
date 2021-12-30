proxy:
	symfony proxy:start &

dns:
	symfony proxy:domain:attach personal-library &

serve:
	symfony serve --no-tls &

docker:
	docker-compose up -d

all: proxy dns serve docker
	npm run watch
