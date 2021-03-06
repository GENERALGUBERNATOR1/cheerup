module.exports = function (shipit) {
    require('shipit-deploy')(shipit);
    require('shipit-shared')(shipit);

    shipit.initConfig({
        default: {
            workspace: '/tmp/cheerup',
            repositoryUrl: 'git@github.com:harijoe/cheerup.git',
            ignores: ['.git', 'node_modules'],
            keepReleases: 3,
            shallowClone: true,
            shared: {
                overwrite: true,
                dirs: [
                    'vendor',
                    'bower_components',
                    'node_modules',
                    'var'
                ],
                files: [
                    'app/config/parameters.yml'
                ]
            }
        },
        prod: {
            servers: 'ubuntu@52.29.16.189',
            branch: 'master',
            deployTo: '/var/www/cheerup'
        }
    });

    var composerInstall = function () {
        return shipit.remote("cd " + shipit.releasePath + " && composer install");
    };

    var clearCache = function () {
        return shipit.remote("cd " + shipit.releasePath + " && php bin/console cache:clear --env=prod");
    };

    var migrate = function () {
        return shipit.remote("cd " + shipit.releasePath + " && php bin/console doctrine:schema:update --force");
    };

    var npmInstall = function () {
        return shipit.remote("cd " + shipit.releasePath + " && npm install");
    };

    var bowerInstall = function () {
        return shipit.remote("cd " + shipit.releasePath + " && ./node_modules/.bin/bower install");
    };

    var gulpBuild = function () {
        return shipit.remote("cd " + shipit.releasePath + " && ./node_modules/.bin/gulp build");
    };

    var fpmRestart = function() {
        return shipit.remote("sudo service php5-fpm restart");
    };

    shipit.on('shared:link:files', function() {
        return shipit.start('install');
    });

    shipit.blTask('install', function() {
        return composerInstall()
            .then(clearCache)
            .then(migrate)
            .then(npmInstall)
            //.then(bowerInstall)
            .then(gulpBuild)
            .then(fpmRestart)
            .then(function () {
                shipit.log('Install Done!');
            });
    });
};
