package com.example.proycontrol

import okhttp3.MultipartBody
import okhttp3.RequestBody
import retrofit2.Call
import retrofit2.http.*

interface ApiService {
    @FormUrlEncoded
    @POST("api/login.php")
    fun login(
        @Field("correo") correo: String,
        @Field("password") password: String
    ): Call<LoginResponse>

    @Multipart
    @POST("api/registro.php")
    fun registrarUsuario(
        @Part("nombre") nombre: RequestBody,
        @Part("rol") rol: RequestBody,
        @Part("salon") salon: RequestBody,
        @Part("carrera") carrera: RequestBody,
        @Part("correo") correo: RequestBody,
        @Part("password") password: RequestBody,
        @Part credencial: MultipartBody.Part
    ): Call<RegistroResponse>

    @FormUrlEncoded
    @POST("api/solicitar.php")
    fun solicitarProyector(
        @Field("usuario_id") usuarioId: Int
    ): Call<AccionResponse>

    @FormUrlEncoded
    @POST("api/devolver.php")
    fun devolverProyector(
        @Field("usuario_id") usuarioId: Int
    ): Call<AccionResponse>

    @GET("api/dashboard.php")
    fun obtenerDashboard(
        @Query("usuario_id") usuarioId: Int
    ): Call<DashboardResponse>
}