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
    checkAvailability();
};
