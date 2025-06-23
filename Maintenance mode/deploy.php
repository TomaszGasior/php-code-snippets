<?php

namespace Deployer;

set('shared_dirs', ['var/log', 'var/sessions', 'var/lock']);

desc('Enable maintenance mode');
task('maintenance:enable', 'touch var/lock/maintenance.lock');

desc('Disable maintenance mode');
task('maintenance:disable', 'rm -f var/lock/maintenance.lock');
