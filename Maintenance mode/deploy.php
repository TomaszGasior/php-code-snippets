<?php

namespace Deployer;

set('shared_dirs', ['var/lock']);

desc('Enable maintenance mode');
task('maintenance:enable', function() {
    run('touch {{current_path}}/var/lock/maintenance.lock');
});

desc('Disable maintenance mode');
task('maintenance:disable', function() {
    run('rm -f {{current_path}}/var/lock/maintenance.lock');
});
