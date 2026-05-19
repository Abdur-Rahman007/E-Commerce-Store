function checkAvailability(){
    let stock = document.getElementById("stock_qty").value;
    let checkbox = document.getElementById("is_available");
    let text = document.getElementById("availabilityText");

    if(parseInt(stock) <= 0 || stock === ""){
        checkbox.checked = false;
        text.innerHTML = "Out of stock";
        text.style.color = "red";
    }else if(parseInt(stock) <= 5){
        checkbox.checked = true;
        text.innerHTML = "Low stock alert";
        text.style.color = "orange";
    }else{
        checkbox.checked = true;
        text.innerHTML = "In stock";
        text.style.color = "green";
    }
}

window.onload = function(){
    if(document.getElementById("stock_qty")){
        checkAvailability();
    }
};

function toggleAvailability(id){
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            let badge = document.getElementById("availability_" + id);

            if(this.responseText == "1"){
                badge.innerHTML = "In Stock";
                badge.className = "availability-badge stock-in";
            }else if(this.responseText == "0"){
                badge.innerHTML = "Out of Stock";
                badge.className = "availability-badge stock-out";
            }else{
                alert("Availability update failed");
            }
        }
    };

    xhttp.open("POST", "../../../controller/ProductController.php?action=toggleAvailability", true);
    xhttp.setRequestHeader("content-type", "application/x-www-form-urlencoded");
    xhttp.send("id=" + id);
}
