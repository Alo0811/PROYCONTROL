function validarFormulario(){

let nombre =
document.getElementById("nombre").value;

let correo =
document.getElementById("correo").value;

let rol =
document.getElementById("rol").value;

// VALIDAR NOMBRE 

if(nombre.trim()==""){
alert("Ingrese su nombre completo.");
return false;
}

if(nombre.trim().length < 8){
alert("El nombre es demasiado corto. Ingrese nombre y apellidos.");
return false;
}

let palabras = nombre.trim().split(" ");

if(palabras.length < 2){
alert("Ingrese al menos nombre y apellido.");
return false;
}

// VALIDAR ROL 

if(rol==""){
alert("Seleccione un rol.");
return false;
}

// VALIDAR CORREO INSTITUCIONAL 

if(!correo.endsWith("@fcaei.uaem.edu.mx")){
alert("Debe ingresar un correo institucional válido (@fcaei.uaem.edu.mx).");
return false;
}

let archivo =
document.querySelector('input[name="credencial"]').files[0];

if(!archivo){
    alert("Debe seleccionar una credencial.");
    return false;
}

let extensionesPermitidas = [
    "image/jpeg",
    "image/jpg",
    "image/png"
];

if(!extensionesPermitidas.includes(archivo.type)){
    alert("Solo se permiten imágenes JPG, JPEG o PNG.");
    return false;
}

return true;
}

function mostrarImagen(event){

let imagen =
document.getElementById("vista");

imagen.src =
URL.createObjectURL(
event.target.files[0]
);

}