$(document).ready(function() {
    
    // Constants
    var process = '../includes/get_resources.ajax.php';    
    var id = $('.program_id').attr('id');
   
    $('.slice').each(function() {
        
        $this = $(this);
        
        $level = $(this).children('.slice-level').attr('id');
        $unit = $(this).children('.slice-unit').attr('id');
        
        // AJAX
        function ajaxRequest(a,b,c,d,e) {
            $.ajax({
                url: a,
                async: true,
                type: "POST",
                data: {
                    id: b,
                    level: c,
                    unit: d        
                }
            }).done(function(data) {
                e.children('.resources').append(data); 
            });    
        }
        
        ajaxRequest( process, id, $level, $unit, $this );
        
    });
    
});