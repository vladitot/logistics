@servers(['localhost' => '127.0.0.1'])

@setup
    $dockerCompose = 'cd laradock && docker-compose'
@endsetup

@task('tasks', ['on'=>'localhost'])
    envoy tasks
@endtask

@task('laradock', ['on'=>'localhost'])
    rm -rf laradock || true
    git clone https://github.com/Laradock/laradock.git
    cd laradock && git reset --hard 58d7d4fa0bfc6bf8980d1553a72c78aa8c097897 && cd ../
    cp docker/.env laradock/.env
@endtask

@task('up', ['on' => 'localhost'])
    {{ $dockerCompose }} up -d workspace php-fpm nginx redis mysql phpmyadmin
@endtask


@task('down', ['on'=>'localhost'])
    {{ $dockerCompose }} down
@endtask

@task('logs', ['on'=>'localhost'])
    {{ $dockerCompose }} logs -ft {{ $containers }}
@endtask


@task('restart', ['on'=>'localhost'])
    {{ $dockerCompose }} restart {{ $containers }}
@endtask

@task('bash', ['on'=>'localhost'])
    {{ $dockerCompose }} exec -u laradock workspace bash
@endtask

@task('xdebug-on', ['on'=>'localhost'])
    ./laradock/php-fpm/xdebug start
@endtask

@task('xdebug-off', ['on'=>'localhost'])
    ./laradock/php-fpm/xdebug stop
@endtask

@task('rebuild', ['on'=>'localhost'])
    @if ($containers)
        {{ $dockerCompose }} build --no-cache {{ $containers }}
    @else
        echo "nothing to build. specify --containers=\"php-fpm nginx etc1 etc2\""
    @endif
@endtask

@task('build', ['on'=>'localhost'])
    @if ($containers)
        {{ $dockerCompose }} build {{ $containers }}
    @else
        echo "nothing to build. specify --containers=\"php-fpm nginx etc1 etc2\""
    @endif
@endtask

