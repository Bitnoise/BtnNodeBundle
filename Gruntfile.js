/*global module:false*/
module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        jshint: {
            options: {
                jshintrc: '.jshintrc'
            },
            bundle: ['*.json', 'Gruntfile.js']
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
                rulesets: 'phpmd-rules.xml'
            },
            bundle: {
                dir: 'src/'
            }
        }

    });

    // These plugins provide necessary tasks.
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-php-cs-fixer');
    grunt.loadNpmTasks('grunt-phpcs');
    grunt.loadNpmTasks('grunt-phpmd');

    // Default task.
    grunt.registerTask('test', ['jshint', 'phpcsfixer', 'phpmd']);
    grunt.registerTask('default', ['test']);
};
