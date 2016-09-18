module.exports = function(grunt) {
  require('jit-grunt')(grunt);

  grunt.initConfig({
       
    synchard: {
        remotedest: {
            options: {
            	args: ['-av','--delete'],
            	exclude: ['language','css'],
                ssh: true,
                privateKey: "/home/mrfg/.ssh/Area51.pem"
            },
            files: {
            	'ubuntu@area51.ukriversguidebook.co.uk:/var/www/ukrgb/phpbb/ext/jfusion': ['components/com_jfusion/plugins/phpbb31/jfusion/'],
            	'ubuntu@area51.ukriversguidebook.co.uk:/var/www/ukrgb/joomla/components/com_jfusion/': ['components/com_jfusion/'],
            	'ubuntu@area51.ukriversguidebook.co.uk:/var/www/ukrgb/joomla/administrator/components/com_jfusion/': ['administrator/components/com_jfusion/'],                
            }
        }
    },

    watch: {
      site: {
        files: ['components/**/*','administrator/**/*'], // which files to watch
        tasks: ['synchard'],
        options: {
          nospawn: true
        }
      }
    }
  });
  
  grunt.file.setBase('../../org.jfusion.jfusion/')
  
  grunt.registerTask('sync', ['synchard']);

  grunt.registerTask('default', ['watch']);
};