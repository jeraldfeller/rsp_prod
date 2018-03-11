var XMLHttpRequestObject = false;

if (window.XMLHttpRequest)
{
    XMLHttpRequestObject = new XMLHttpRequest();
}
else if (window.ActiveXObject)
{
    XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
}

function showDeferredTotal(data){

    if(XMLHttpRequestObject)
    {

        XMLHttpRequestObject.open("POST", "../../lib/deffered/get_deferred_data.php");


        XMLHttpRequestObject.setRequestHeader('Content-Type','application/x-www-form-urlencoded');

        XMLHttpRequestObject.onreadystatechange = function()
        {
            if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200)
            {
                var response =  $.parseJSON(XMLHttpRequestObject.responseText);


                console.log(response);

                $('#image-modal-product-name').html(response['details']['name'] + ' <span class="pull-right"> <a href="' + response['details']['url'] + '" target="_blank">view store <i class="fa fa-location-arrow"></i></a></span>');


                $('.image-in-modal').on('click', function(){
                    $('#previewImage').attr("src", $(this).data('url-zoom'));

                })

                var oTable = $('#datatable-responsive').DataTable();
                oTable.ajax.reload();

            }

            if (XMLHttpRequestObject.status == 408 || XMLHttpRequestObject.status == 503){
            }
        }


        XMLHttpRequestObject.send("param="+ data);


    }

    return false;
}