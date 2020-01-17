<?php
namespace Deployer;

require 'recipe/symfony.php';

// Project name
set('application', 'personal-library');

// Project repository
set('repository', 'git@github.com:RaphaelSalique/personal-library.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
add('shared_files', ['.env']);
add('shared_dirs', ['var']);

// Writable dirs by web server
add('writable_dirs', []);


// Hosts

host('personal-library.salique.fr')
    ->port('2707')
    ->set('deploy_path', '~/{{application}}');

// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'database:migrate');

