package com.example.proycontrol

import android.content.Context
import android.graphics.Color
import android.os.Bundle
import android.view.Gravity
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.navigation.fragment.findNavController
import com.example.proycontrol.databinding.FragmentSecondBinding
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class SecondFragment : Fragment() {

    private var _binding: FragmentSecondBinding? = null
    private val binding get() = _binding!!
    private var usuarioId: Int = 0

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = FragmentSecondBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        val prefs = requireActivity().getSharedPreferences("sesion_proy", Context.MODE_PRIVATE)
        usuarioId = prefs.getInt("id", 0)
        val rol = prefs.getString("rol", "Jefe")
        binding.tvBienvenidaRol.text = "Bienvenido $rol"

        cargarDatosDashboard()

        binding.btnSalirDashboard.setOnClickListener {
            prefs.edit().clear().apply()
            findNavController().navigate(R.id.action_SecondFragment_to_FirstFragment)
        }

        binding.btnSolicitarProyector.setOnClickListener {
            RetrofitClient.instance.solicitarProyector(usuarioId)
                .enqueue(object : Callback<AccionResponse> {
                    override fun onResponse(call: Call<AccionResponse>, response: Response<AccionResponse>) {
                        val body = response.body()
                        if (response.isSuccessful && body != null) {
                            val mensaje = if (body.status == "success") "Solicitud enviada" else (body.message ?: "Error al solicitar")
                            Toast.makeText(requireContext(), mensaje, Toast.LENGTH_LONG).show()
                        } else {
                            Toast.makeText(requireContext(), "Error servidor (${response.code()})", Toast.LENGTH_LONG).show()
                        }
                        cargarDatosDashboard()
                    }

                    override fun onFailure(call: Call<AccionResponse>, t: Throwable) {
                        Toast.makeText(requireContext(), "Sin conexión: ${t.message}", Toast.LENGTH_SHORT).show()
                    }
                })
        }

        binding.btnDevolverProyector.setOnClickListener {
            RetrofitClient.instance.devolverProyector(usuarioId)
                .enqueue(object : Callback<AccionResponse> {
                    override fun onResponse(call: Call<AccionResponse>, response: Response<AccionResponse>) {
                        val body = response.body()
                        if (response.isSuccessful && body != null) {
                            val mensaje = if (body.status == "success") "Proyector devuelto correctamente" else (body.message ?: "No tienes proyectores pendientes")
                            Toast.makeText(requireContext(), mensaje, Toast.LENGTH_LONG).show()
                        } else {
                            Toast.makeText(requireContext(), "Error servidor (${response.code()})", Toast.LENGTH_LONG).show()
                        }
                        cargarDatosDashboard()
                    }

                    override fun onFailure(call: Call<AccionResponse>, t: Throwable) {
                        Toast.makeText(requireContext(), "Sin conexión: ${t.message}", Toast.LENGTH_SHORT).show()
                    }
                })
        }
    }

    private fun cargarDatosDashboard() {
        RetrofitClient.instance.obtenerDashboard(usuarioId)
            .enqueue(object : Callback<DashboardResponse> {
                override fun onResponse(call: Call<DashboardResponse>, response: Response<DashboardResponse>) {
                    val body = response.body() ?: return
                    binding.tvProyectoresDisponibles.text = body.disponibles.toString()

                    binding.contenedorFilasSolicitudes.removeAllViews()
                    for (item in body.solicitudes) {
                        val fila = LinearLayout(requireContext()).apply {
                            orientation = LinearLayout.HORIZONTAL
                            setPadding(8, 18, 8, 18)
                            setBackgroundColor(Color.WHITE)
                        }
                        fila.addView(crearCelda(item.fecha ?: "---", 1f))
                        fila.addView(crearCelda(item.hora ?: "---", 1f))
                        fila.addView(crearCelda(item.estado ?: "---", 1f))
                        fila.addView(crearCelda(item.fecha_devolucion ?: "Pendiente", 1.2f))
                        binding.contenedorFilasSolicitudes.addView(fila)
                    }
                }

                override fun onFailure(call: Call<DashboardResponse>, t: Throwable) {}
            })
    }

    private fun crearCelda(texto: String, peso: Float): TextView {
        return TextView(requireContext()).apply {
            text = texto
            gravity = Gravity.CENTER
            textSize = 12f
            setTextColor(Color.BLACK)
            layoutParams = LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.WRAP_CONTENT, peso)
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}