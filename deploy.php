<?php
namespace Deployer;

require 'recipe/symfony4.php';

// Project name
set('application', 'personal-library');

// Project repository
set('repository', 'git@github.com:RaphaelSalique/personal-library.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
add('shared_files', ['.env.local']);

// Writable dirs by web server
//add('writable_dirs', []);

set('writable_dirs', ['var/cache', 'var/log', 'var/sessions']);

// Hosts

host('personal-library.salique.fr')
    ->user('ubuntu')
    ->port('2707')
    ->set('deploy_path', '~/{{application}}');


// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'database:migrate');

