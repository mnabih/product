/*Choose Cities For Current Country Only*/
function getCities(){
    $('#country option:selected').each(function(){
       var id = $(this).attr('country-id');
        $('#city option').each(function(){
            if($(this).attr('country-id') == id)
            {
                $(this).css('display','block');
            }else
            {
                $(this).css('display','none');
            }
        })
    })
}