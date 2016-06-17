module.exports = function(grunt) {
  require('jit-grunt')(grunt);

  grunt.initConfig({
       
    synchard: {
        remotedest: {
            options: {
                ssh: true,
                privateKey: "/home/mrfg/.ssh/Area51.pem"
            },
            files: {
                'ubuntu@area51.ukriversguidebook.co.uk:/var/www/ukrgb/phpbb/ext/': ['ext/ukrgb']
            }
        }
    },

    watch: {
      styles: {
        files: ['ext/**/*.less'], // which files to watch
        tasks: ['synchard'],
        options: {
          nospawn: true
        }
      }
    }
  });
  grunt.registerTask('sync', ['synchard']);

  grunt.registerTask('default', ['watch']);
};