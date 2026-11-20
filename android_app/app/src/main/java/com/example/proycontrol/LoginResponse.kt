package com.example.proycontrol

data class LoginResponse(
    val status: String,
    val message: String,
    val usuario: UsuarioData? = null
)

data class UsuarioData(
    val id: Int,
    val nombre: String,
    val rol: String,
    val salon: String,
    val carrera: String,
    val correo: String
)

data class RegistroResponse(
    val status: String,
    val message: String,
    val usuario_id: Int? = null,
    val nombre: String? = null,
    val rol: String? = null
)

data class AccionResponse(
    val status: String,
    val message: String
)

data class DashboardResponse(
    val status: String,
    val disponibles: Int,
    val solicitudes: List<SolicitudItem>
)

data class SolicitudItem(
    val fecha: String,
    val hora: String,
    val estado: String,
    val fecha_devolucion: String
)