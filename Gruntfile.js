module.exports = function(grunt) {
    'use strict';

    require('load-grunt-tasks')(grunt);

    var php_files = ['*.php', 'php/**/*.php'];

    grunt.initConfig({

        checktextdomain: {
            standard: {
                options:{
                    text_domain: 'functionality',
                    keywords: [
                        '__:1,2d', '_e:1,2d', '_x:1,2c,3d',
                        'esc_html__:1,2d', 'esc_html_e:1,2d', 'esc_html_x:1,2c,3d',
                        'esc_attr__:1,2d', 'esc_attr_e:1,2d', 'esc_attr_x:1,2c,3d',
                        '_ex:1,2c,3d', '_n:1,2,4d', '_nx:1,2,4c,5d',
                        '_n_noop:1,2,3d', '_nx_noop:1,2,3c,4d'
                    ]
                },
                files: [{
                    src: php_files,
                    expand: true
                }]
            }
        },

        pot: {
            options: {
                text_domain: 'functionality',
                dest: 'languages/',
                keywords: ['__','_e','esc_html__','esc_html_e','esc_attr__', 'esc_attr_e', 'esc_attr_x', 'esc_html_x', 'ngettext', '_n', '_ex', '_nx']
            },
            files: {
                src: php_files,
                expand: true
            }
        },

        po2mo: {
            files: {
                src: 'languages/*.po',
                expand: true
            }
        },

        clean: {
            package: ['package']
        },

        copy: {
            package: {
                src: [
                    'readme.txt',
                    '*.php',
                    'php/**/*',
                    'languages/**/*'
                ],
                dest: 'package',
                expand: true
            }
        }
    });

    grunt.registerTask( 'l18n', ['checktextdomain', 'pot', 'newer:po2mo'] );
    grunt.registerTask( 'package', ['clean:package', 'copy:package'] );
    grunt.registerTask( 'default', ['l18n', 'package'] );
};
