/*global module:false*/
module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        jshint: {
            options: {
                jshintrc: 'tests-settings/.jshintrc'
            },
            bundle: ['src/Btn/NodeBundle/Resources/public/js/**/*.js', '*.json', 'Gruntfile.js']
        },

        csslint: {
            options: {
                csslintrc: 'tests-settings/.csslintrc',
            },
            bundle: {
                src: ['src/Btn/NodeBundle/Resources/public/css/**/*.css']
            }
        },

        phpcsfixer: {
            options: {
                bin: 'vendor/bin/php-cs-fixer',
                level: 'all',
                ignoreExitCode: false,
                verbose: true,
                diff: false,
                dryRun: true
            },
            bundle: {
                dir: 'src/'
            }
        },

        phpcs: {
            options: {
                bin: 'vendor/bin/phpcs',
                extensions: 'php',
                standard: 'PSR2'
            },
            bundle: {
                dir: ['src/']
            }
        },

        phpmd: {
            options: {
                bin: 'vendor/bin/phpmd',
                reportFormat: 'text',
                rulesets: 'tests-settings/phpmd-rules.xml'
            },
            bundle: {
                dir: 'src/'
            }
        },

        phpcpd: {
            options: {
                bin: 'vendor/bin/phpcpd',
                quiet: true
            },
            bundle: {
                dir: 'src/'
            }
        },

        exec: {
          twig_lint: 'vendor/bin/twig-lint lint src/ --ansi'
        }

    });

    // These plugins provide necessary tasks.
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-csslint');
    grunt.loadNpmTasks('grunt-php-cs-fixer');
    grunt.loadNpmTasks('grunt-phpcs');
    grunt.loadNpmTasks('grunt-phpmd');
    grunt.loadNpmTasks('grunt-phpcpd');
    grunt.loadNpmTasks('grunt-exec');

    // Default task.
    grunt.registerTask('test', ['jshint', 'csslint', 'phpcsfixer', 'phpmd', 'phpcpd', 'phpcs', 'exec:twig_lint']);
    grunt.registerTask('default', ['test']);
};
