
window.onload = (event) => {
    const params = new URLSearchParams(document.location.search);
    const email = params.get("email");
    
    document.getElementById("email").value=email;
    
    document.getElementById("send-mail").addEventListener("click",(event)=>{
        event.preventDefault();
        
        const emailpat=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        const res=emailpat.test(email);
    
        if(res){
            let xhr= new XMLHttpRequest();
            document.getElementById("send-mail").disabled=true;
            document.getElementById("result").innerHTML="Please wait sending otp...";
            xhr.open('POST','https://xkcd-comic-php-project.herokuapp.com/sendOTP.php',true);
    
            xhr.setRequestHeader("Accept", "application/json");
            xhr.setRequestHeader("Content-Type", "application/json");
        
            xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                console.log(xhr.responseText);
                if(xhr.responseText){
                    document.getElementById("result").innerHTML=xhr.responseText;
                    document.getElementById("send-mail").disabled=false;
                }
            }};
            var data = JSON.stringify({email:email});
            xhr.send(data);
        }
        else{
            document.getElementById("result").innerHTML="Invalid Access";
            
        }
    })

    document.getElementById("mail-check").addEventListener("click",(event)=>{
        event.preventDefault();
        
        const emailpat=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        const res=emailpat.test(email);
        const otp=Number(document.getElementById("OTP").value);

        if(res){
            let xhr= new XMLHttpRequest();
            document.getElementById("mail-check").disabled=true;
            document.getElementById("result").innerHTML="Please wait unsubscribing the user...";
            xhr.open('POST','https://xkcd-comic-php-project.herokuapp.com/confirmOTP.php',true);
    
            xhr.setRequestHeader("Accept", "application/json");
            xhr.setRequestHeader("Content-Type", "application/json");
        
            xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                console.log(xhr.responseText);
                if(xhr.responseText){
                    document.getElementById("result").innerHTML=xhr.responseText;
                    document.getElementById("mail-check").disabled=false;
                }
                
            }};
    
            var data = JSON.stringify({"email":email,"OTP":otp});

            xhr.send(data);
            
        }
        else{
            document.getElementById("result").innerHTML="Invalid Access";
        }
    
    
    })
};

