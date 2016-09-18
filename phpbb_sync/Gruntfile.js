module.exports = function(grunt) {
  require('jit-grunt')(grunt);

  grunt.initConfig({
       
    synchard: {
        remotedest: {
            options: {
            	args: ['-av'],
            	//exclude: ['language','css'],
                ssh: true,
                privateKey: "/home/mrfg/.ssh/Area51.pem"
            },
            files: {
            	'ubuntu@area51.ukriversguidebook.co.uk:/var/www/ukrgb/phpbb/phpbb/auth/': ['phpbb/auth/'],
            	'ubuntu@area51.ukriversguidebook.co.uk:/var/www/ukrgb/phpbb/includes/': ['includes/'],
            	'ubuntu@area51.ukriversguidebook.co.uk:/var/www/ukrgb/phpbb/vendor/': ['vendor/'],
            	'ubuntu@area51.ukriversguidebook.co.uk:/var/www/ukrgb/phpbb/': ['ucp.php'],
            }
        }
    },

    watch: {
      site: {
        files: ['phpbb/**/*.php','includes/**/*.php','vendor/**/*.php','*.php'], // which files to watch
        tasks: ['synchard'],
        options: {
          nospawn: true
        }
      }
    }
  });
  
  grunt.file.setBase('../../phpbb/phpBB/')
  
  grunt.registerTask('sync', ['synchard']);

  grunt.registerTask('default', ['watch']);
};