$(function(){

    var $ftr = $('.footer');
    if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight()){
      $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;'});
    }

    var $areadrop = $('.area-drop');
    var $inputfile = $('.input-file');
    
    $areadrop.on('dragover', function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border','#e4e3e3 dotted');
    });

    $areadrop.on('dragleave', function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border', 'none');
    });

    $inputfile.on('change', function(){
        $areadrop.css('border', 'none');

        var file = this.files[0],
            $img = $(this).siblings('.prev-img'),
            fileReader = new FileReader();

        fileReader.onload = function(event){
            $img.attr('src', event.target.result).show();
        };
        fileReader.readAsDataURL(file);
    });

    $('.js-count').keyup(function(){
        var count = $(this).val().length;
        $('.showCount').text(count);
    });
  });

