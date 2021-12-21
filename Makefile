ARGS = $(filter-out $@,$(MAKECMDGOALS))
MAKEFLAGS += --silent
COMPOSEFILE="docker/docker-compose.yml"

list:
	sh -c "echo; $(MAKE) -p no_targets__ | awk -F':' '/^[a-zA-Z0-9][^\$$#\/\\t=]*:([^=]|$$)/ {split(\$$1,A,/ /);for(i in A)print A[i]}' | grep -v '__\$$' | grep -v 'Makefile'| sort"

#############################
# Docker machine states
#############################
up:
	docker start dev_mariadb && docker-compose -f $(COMPOSEFILE) up -d --force-recreate

start:
	docker start dev_mariadb && docker-compose -f $(COMPOSEFILE) start

stop:
	docker stop dev_mariadb && docker-compose -f $(COMPOSEFILE) stop

down:
	docker stop dev_mariadb && docker-compose -f $(COMPOSEFILE) down

rebuild:
	docker-compose -f $(COMPOSEFILE) stop
	docker-compose -f $(COMPOSEFILE) pull
	docker-compose -f $(COMPOSEFILE) rm --force app
	docker-compose -f $(COMPOSEFILE) build --no-cache --pull
	docker-compose -f $(COMPOSEFILE) up -d --force-recreate

#############################
# General
#############################
shell:
	docker-compose -f $(COMPOSEFILE) exec --user application app /bin/bash

root:
	docker-compose -f $(COMPOSEFILE) exec --user root app /bin/bash

#############################
# Argument fix workaround
#############################
%:
	@:
