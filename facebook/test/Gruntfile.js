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
                'ubuntu@area51.ukriversguidebook.co.uk:/var/www/fb': ['vendor','src/']
            }
        }
    },

    watch: {
      styles: {
        files: ['vendor/**/*','src/**/*'], // which files to watch
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