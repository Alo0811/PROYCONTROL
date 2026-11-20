package com.example.proycontrol

import android.content.Context
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.navigation.fragment.findNavController
import com.example.proycontrol.databinding.FragmentFirstBinding
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class FirstFragment : Fragment() {

    private var _binding: FragmentFirstBinding? = null
    private val binding get() = _binding!!

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = FragmentFirstBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        binding.tvIrRegistro.setOnClickListener {
            findNavController().navigate(R.id.action_FirstFragment_to_registroFragment)
        }

        binding.btnLogin.setOnClickListener {
            val correo = binding.etCorreo.text.toString().trim()
            val password = binding.etPassword.text.toString().trim()

            if (correo.isEmpty() || password.isEmpty()) {
                Toast.makeText(requireContext(), "Completa correo y contraseña", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            binding.btnLogin.isEnabled = false

            RetrofitClient.instance.login(correo, password)
                .enqueue(object : Callback<LoginResponse> {
                    override fun onResponse(
                        call: Call<LoginResponse>,
                        response: Response<LoginResponse>
                    ) {
                        binding.btnLogin.isEnabled = true
                        val body = response.body()

                        if (response.isSuccessful && body != null) {
                            if (body.status == "success") {
                                val prefs = requireActivity().getSharedPreferences("sesion_proy", Context.MODE_PRIVATE)
                                prefs.edit()
                                    .putInt("id", body.usuario?.id ?: 0)
                                    .putString("nombre", body.usuario?.nombre ?: "")
                                    .putString("rol", body.usuario?.rol ?: "Usuario")
                                    .apply()

                                Toast.makeText(requireContext(), "Bienvenido ${body.usuario?.nombre}", Toast.LENGTH_SHORT).show()
                                findNavController().navigate(R.id.action_FirstFragment_to_SecondFragment)
                            } else {
                                Toast.makeText(requireContext(), body.message, Toast.LENGTH_LONG).show()
                            }
                        } else {
                            Toast.makeText(requireContext(), "Error del servidor (${response.code()})", Toast.LENGTH_SHORT).show()
                        }
                    }

                    override fun onFailure(call: Call<LoginResponse>, t: Throwable) {
                        binding.btnLogin.isEnabled = true
                        Toast.makeText(requireContext(), "Sin conexión: ${t.message}", Toast.LENGTH_LONG).show()
                    }
                })
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}