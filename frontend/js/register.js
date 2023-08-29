
document.getElementById("submit").addEventListener("click",(event)=>{
    event.preventDefault();
    const emailpat=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    const email=document.getElementById("user-email").value;
    const res=emailpat.test(email);

    if(res){
        document.getElementById("submit").disabled=true;
        let xhr= new XMLHttpRequest();
        document.getElementById("result").innerHTML="Please Wait Registering user...";
        xhr.open('POST','https://xkcd-comic-php-project.herokuapp.com/register_user.php',true);

        xhr.setRequestHeader("Accept", "application/json");
        xhr.setRequestHeader("Content-Type", "application/json");
    
        xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            console.log(xhr.responseText);
            document.getElementById("result").innerHTML=xhr.responseText;
            document.getElementById("submit").disabled=false;
        }};

        var data = JSON.stringify({email:email});
            
        xhr.send(data);

    }
    else{
        document.getElementById("result").innerHTML="Enter All Values";
    }


})
