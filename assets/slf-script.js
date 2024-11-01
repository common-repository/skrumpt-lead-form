var postcode_addresses = [];

// Fetch addresses from postcode
var address_lists = document.getElementById('slf_address_lists');

// Get the modal
var skrumpt_modal = document.getElementById("skrumpt_modal");

// Get the <span> element that closes the modal
var skrumpt_close_modal = document.getElementsByClassName("slf_modal_close")[0];

// Steps

var step1 = document.getElementById("slf_step_1");
var step2 = document.getElementById("slf_step_2");
var step3 = document.getElementById("slf_step_3");


// When the user clicks on <span> (x), close the modal
skrumpt_close_modal.onclick = function() {
    skrumpt_modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == skrumpt_modal) {
    skrumpt_modal.style.display = "none";
  }
}

jQuery.noConflict()(function($){

    function validateEmail(email) {
        const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }
    
    function clearInputs(){
        $('#slf_title').val("") 
        $('#slf_title').val("")
        $('#slf_firstname').val("")
        $('#slf_lastname').val("")
        $('#slf_telephone').val("")
        $('#slf_mobile').val("")
        $('#slf_email').val("")
        $('#slf_address').val("")
        $('#slf_city').val("")
        $('#slf_county').val("")
        $('#slf_country').val("")
        $('#slf_postcode').val("")
        $('#slf_propertytype').val("")
        $('#slf_estimatedvalue').val("")
        $('#slf_estimatedsecureddebts').val("")
        $('#slf_reasonforselling').val("")
    }

    $('#slf_address_lists').on('change', function() {

        var address_values = postcode_addresses[this.value]
        var address_value = address_values.split(',');

        $("#slf_address").val(address_value[0]);
        $("#slf_city").val(address_value[5]);
        $("#slf_county").val(address_value[6]);
    });

    $(".skrumpt_input").change(function(event){    
        $(".skrumpt_input").val(this.value);
    });

    $("#skrumpt_search").click(function(event){    
  
        event.preventDefault();

        try {

            $(".success_container").css("display", "none");
            $(".slf_form").css("display", "block");

            
            $('#skrumpt_search').attr('disabled', 'disabled');
            $('#skrumpt_input').attr('disabled', 'disabled');
            
            if($('#skrumpt_input').val() == null || $('#skrumpt_input').val() == ""){
                throw("Please enter postcode")
            }
    
            $.ajax({
                url : slf_object.ajax_url,
                type: "POST",
                data : {
                    "action": 'postcodelookup',
                    "nonce": slf_object.postcode_nonce,
                    "postcode": $('#skrumpt_input').val(),
                },
                success: function(response)
                {
                    $('#skrumpt_search').removeAttr('disabled');
                    $('#skrumpt_input').removeAttr('disabled');

                    if(response.success){
                        
                        skrumpt_modal.style.display = "block";
                        postcode_addresses = response.details.addresses

                        $("#slf_postcode").val($('#skrumpt_input').val().toUpperCase());

                    }else{
                        alert(response.message)
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $('#skrumpt_search').removeAttr('disabled');
                    $('#skrumpt_input').removeAttr('disabled');

                    alert("Something went wrong. Please try again later");
                }
            });
        } catch (error) {
            $('#skrumpt_search').attr('disabled', 'disabled');
            $('#skrumpt_input').attr('disabled', 'disabled');


            alert(error)
        }
    })

    $(".skrumpt_search").click(function(event){    
  
        event.preventDefault();

        try {

            $(".success_container").css("display", "none");
            $(".slf_form").css("display", "block");

            
            $('.skrumpt_search').attr('disabled', 'disabled');
            $('.skrumpt_input').attr('disabled', 'disabled');
            
            if($('.skrumpt_input').val() == null || $('.skrumpt_input').val() == ""){
                throw("Please enter postcode")
            }
    
            $.ajax({
                url : slf_object.ajax_url,
                type: "POST",
                data : {
                    "action": 'postcodelookup',
                    "nonce": slf_object.postcode_nonce,
                    "postcode": $('.skrumpt_input').val(),
                },
                success: function(response)
                {
                    $('.skrumpt_search').removeAttr('disabled');
                    $('.skrumpt_input').removeAttr('disabled');

                    if(response.success){
                        skrumpt_modal.style.display = "block";
                        postcode_addresses = response.details.addresses

                        $("#slf_postcode").val($('.skrumpt_input').val().toUpperCase());

                    }else{
                        alert(response.message)
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $('.skrumpt_search').removeAttr('disabled');
                    $('.skrumpt_input').removeAttr('disabled');

                    alert("Something went wrong. Please try again later");
                }
            });
        } catch (error) {
            $('.skrumpt_search').attr('disabled', 'disabled');
            $('.skrumpt_input').attr('disabled', 'disabled');

            alert(error)
        }
    })



    $("#slf_next_1").click(function(event){    
        event.preventDefault();

        console.log(postcode_addresses)

        try {
            
            if($('#slf_title').val() == null || $('#slf_title').val() == ""){
                throw("Please select owner title")
            }
            if($('#slf_firstname').val() == null || $('#slf_firstname').val() == ""){
                throw("Please select enter firstname")
            }
            if($('#slf_lastname').val() == null || $('#slf_lastname').val() == ""){
                throw("Please select enter lastname")
            }
            if($('#slf_telephone').val() == null || $('#slf_telephone').val() == ""){
                throw("Please select enter telephone")
            }
            if($('#slf_mobile').val() == null || $('#slf_mobile').val() == ""){
                throw("Please select enter mobile")
            }
            if($('#slf_email').val() == null || $('#slf_email').val() == ""){
                throw("Please select enter email")
            }

            if(!validateEmail($('#slf_email').val())){
                throw("The email you entered is invalid")
            }

            step1.style.display = "none";
            step2.style.display = "block";
            step3.style.display = "none";
            

            for(var i = 0, l = postcode_addresses.length; i < l; i++){
                var option = postcode_addresses[i];
                var address_value = option.split(',');
                
                var el = document.createElement("option");
                el.textContent = address_value[0];
                el.value = i;
                address_lists.appendChild(el);
            }


        } catch (error) {

            alert(error)
        }
    });

    $("#slf_next_2").click(function(event){    
        event.preventDefault();

        try {
            
            if($('#slf_address').val() == null || $('#slf_address').val() == ""){
                throw("Please select enter property address")
            }
            if($('#slf_city').val() == null || $('#slf_city').val() == ""){
                throw("Please select enter city")
            }
            if($('#slf_county').val() == null || $('#slf_county').val() == ""){
                throw("Please select enter county")
            }
            if($('#slf_country').val() == null || $('#slf_country').val() == ""){
                throw("Please select enter country")
            }

            if($('#slf_postcode').val() == null || $('#slf_postcode').val() == ""){
                throw("Please select enter postcode")
            }

            step1.style.display = "none";
            step2.style.display = "none";
            step3.style.display = "block";
    
        } catch (error) {

            alert(error)
        }
    });

    $("#slf_previous_2").click(function(event){    
        event.preventDefault();

        try {

            step1.style.display = "block";
            step2.style.display = "none";
            step3.style.display = "none";
    
        } catch (error) {

            alert(error)
        }
    });
    

    $("#slf_form").submit(function(event){    
        
        event.preventDefault();

        try {
            
            $('.slf_fields').attr('disabled', 'disabled');
            $('.slf_submit').attr('disabled', 'disabled');
            
            if($('#slf_title').val() == null || $('#slf_title').val() == ""){
                throw("Please select owner title")
            }
            if($('#slf_firstname').val() == null || $('#slf_firstname').val() == ""){
                throw("Please select enter firstname")
            }
            if($('#slf_lastname').val() == null || $('#slf_lastname').val() == ""){
                throw("Please select enter lastname")
            }
            if($('#slf_telephone').val() == null || $('#slf_telephone').val() == ""){
                throw("Please select enter telephone")
            }
            if($('#slf_mobile').val() == null || $('#slf_mobile').val() == ""){
                throw("Please select enter mobile")
            }
            if($('#slf_email').val() == null || $('#slf_email').val() == ""){
                throw("Please select enter email")
            }
            if(!validateEmail($('#slf_email').val())){
                throw("The email you entered is invalid")
            }
            if($('#slf_address').val() == null || $('#slf_address').val() == ""){
                throw("Please select enter property address")
            }
            if($('#slf_city').val() == null || $('#slf_city').val() == ""){
                throw("Please select enter city")
            }
            if($('#slf_county').val() == null || $('#slf_county').val() == ""){
                throw("Please select enter county")
            }
            if($('#slf_country').val() == null || $('#slf_country').val() == ""){
                throw("Please select enter country")
            }
            if($('#slf_postcode').val() == null || $('#slf_postcode').val() == ""){
                throw("Please select enter postcode")
            }
            if($('#slf_propertytype').val() == null || $('#slf_propertytype').val() == ""){
                throw("Please select select property type")
            }
            
            $.ajax({
                url : slf_object.ajax_url,
                type: "POST",
                data : {
                    "action": 'postproperty',
                    "nonce": slf_object.nonce,
                    "campaign_id": slf_object.campaign_id,
                    "title": $('#slf_title').val(),
                    "firstname": $('#slf_firstname').val(),
                    "lastname": $('#slf_lastname').val(),
                    "telephone": $('#slf_telephone').val(),
                    "mobile": $('#slf_mobile').val(),
                    "email": $('#slf_email').val(),
    
                    "address": $('#slf_address').val(),
                    "city": $('#slf_city').val(),
                    "county": $('#slf_county').val(),
                    "country": $('#slf_country').val(),
                    "postcode": $('#slf_postcode').val(),
    
                    "type": $('#slf_propertytype').val(),
                    "estimatedValue": $('#slf_estimatedvalue').val(),
                    "estimatedSecuredDebts": $('#slf_estimatedsecureddebts').val(),
                    "reasonForSelling": $('#slf_reasonforselling').val(),
                },
                success: function(response)
                {
                    $('.slf_fields').removeAttr('disabled');
                    $('.slf_submit').removeAttr('disabled');

                    if(response.success){
                        clearInputs();

                        if(slf_object.success_redirect_url != ""){
                            window.location.href = slf_object.success_redirect_url;
                        }else{
                            $(".success_container").css("display", "block");
                            $(".slf_form").css("display", "none");
                            $('html, body').animate({
                                scrollTop: $("#slf_form").offset().top
                            }, 500);
                        }


                    }else{
                        alert(response.message)
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $('.slf_fields').removeAttr('disabled');
                    alert("Something went wrong. Please try again later");
                }
            });
        } catch (error) {
            $('.slf_fields').removeAttr('disabled');
            $('.slf_submit').removeAttr('disabled');
            alert(error)
        }
    });

    $("#slf_previous_3").click(function(event){    
        event.preventDefault();

        try {

            step1.style.display = "none";
            step2.style.display = "block";
            step3.style.display = "none";
    
        } catch (error) {

            alert(error)
        }
    });
});