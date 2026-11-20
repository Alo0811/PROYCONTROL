package com.example.proycontrol

import android.content.Context
import android.net.Uri
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ArrayAdapter
import android.widget.Toast
import androidx.activity.result.contract.ActivityResultContracts
import androidx.fragment.app.Fragment
import androidx.navigation.fragment.findNavController
import coil.load
import com.example.proycontrol.databinding.FragmentRegistroBinding
import com.google.mlkit.vision.common.InputImage
import com.google.mlkit.vision.text.TextRecognition
import com.google.mlkit.vision.text.latin.TextRecognizerOptions
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import okhttp3.RequestBody.Companion.toRequestBody
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.io.File

class RegistroFragment : Fragment() {

    private var _binding: FragmentRegistroBinding? = null
    private val binding get() = _binding!!
    private var imagenUri: Uri? = null
    private var credencialValida: Boolean = false

    private val seleccionarImagenLauncher =
        registerForActivityResult(ActivityResultContracts.GetContent()) { uri: Uri? ->
            if (uri != null) {
                validarFotoCredencial(uri)
            }
        }

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = FragmentRegistroBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        val roles = listOf("Jefe", "Subjefe", "Tercero")
        binding.spRegRol.adapter = ArrayAdapter(requireContext(), R.layout.item_spinner_negro, roles)

        val carreras = listOf(
            "Licenciatura en Informática",
            "Licenciatura en Administración",
            "Licenciatura en Contaduría",
            "Licenciatura en Economía"
        )
        binding.spRegCarrera.adapter = ArrayAdapter(requireContext(), R.layout.item_spinner_negro, carreras)

        binding.btnSalirRegistro.setOnClickListener {
            findNavController().navigateUp()
        }

        binding.btnSeleccionarCredencial.setOnClickListener {
            seleccionarImagenLauncher.launch("image/*")
        }

        binding.btnRegistrarEntrar.setOnClickListener {
            val nombre = binding.etRegNombre.text.toString().trim()
            val rol = binding.spRegRol.selectedItem.toString()
            val salon = binding.etRegSalon.text.toString().trim()
            val carrera = binding.spRegCarrera.selectedItem.toString()
            val correo = binding.etRegCorreo.text.toString().trim()
            val password = binding.etRegPassword.text.toString().trim()

            if (nombre.isEmpty() || salon.isEmpty() || correo.isEmpty() || password.isEmpty() || imagenUri == null || !credencialValida) {
                Toast.makeText(requireContext(), "Completa todos los campos y sube una credencial válida", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            val archivoTemp = File(requireContext().cacheDir, "credencial_${System.currentTimeMillis()}.jpg")
            requireContext().contentResolver.openInputStream(imagenUri!!)?.use { input ->
                archivoTemp.outputStream().use { output -> input.copyTo(output) }
            }

            val reqNombre = nombre.toRequestBody("text/plain".toMediaTypeOrNull())
            val reqRol = rol.toRequestBody("text/plain".toMediaTypeOrNull())
            val reqSalon = salon.toRequestBody("text/plain".toMediaTypeOrNull())
            val reqCarrera = carrera.toRequestBody("text/plain".toMediaTypeOrNull())
            val reqCorreo = correo.toRequestBody("text/plain".toMediaTypeOrNull())
            val reqFile = archivoTemp.asRequestBody("image/jpeg".toMediaTypeOrNull())
            val bodyImagen = MultipartBody.Part.createFormData("credencial", archivoTemp.name, reqFile)
            val reqPassword = password.toRequestBody("text/plain".toMediaTypeOrNull()) // <--- 2. EMPAQUETAR

            binding.btnRegistrarEntrar.isEnabled = false

            RetrofitClient.instance.registrarUsuario(reqNombre, reqRol, reqSalon, reqCarrera, reqCorreo, reqPassword, bodyImagen)
                .enqueue(object : Callback<RegistroResponse> {
                    override fun onResponse(call: Call<RegistroResponse>, response: Response<RegistroResponse>) {
                        binding.btnRegistrarEntrar.isEnabled = true
                        val body = response.body()
                        if (response.isSuccessful && body != null && body.status == "success") {
                            val prefs = requireActivity().getSharedPreferences("sesion_proy", Context.MODE_PRIVATE)
                            prefs.edit()
                                .putInt("id", body.usuario_id ?: 0)
                                .putString("nombre", body.nombre ?: nombre)
                                .putString("rol", body.rol ?: rol)
                                .apply()

                            Toast.makeText(requireContext(), body.message, Toast.LENGTH_SHORT).show()
                            findNavController().navigate(R.id.action_registroFragment_to_SecondFragment)
                        } else {
                            Toast.makeText(requireContext(), body?.message ?: "Error al registrar", Toast.LENGTH_LONG).show()
                        }
                    }

                    override fun onFailure(call: Call<RegistroResponse>, t: Throwable) {
                        binding.btnRegistrarEntrar.isEnabled = true
                        Toast.makeText(requireContext(), "Error de red: ${t.message}", Toast.LENGTH_LONG).show()
                    }
                })
        }
    }

    private fun validarFotoCredencial(uri: Uri) {
        try {
            val image = InputImage.fromFilePath(requireContext(), uri)
            val recognizer = TextRecognition.getClient(TextRecognizerOptions.DEFAULT_OPTIONS)

            recognizer.process(image)
                .addOnSuccessListener { visionText ->
                    val textoDetectado = visionText.text.uppercase()
                    val palabrasClave = listOf("UNIVERSIDAD", "UAEM", "CREDENCIAL", "ESTUDIANTE", "FCAEI", "MATRICULA", "FACULTAD", "LICENCIATURA")
                    val esCredencial = palabrasClave.any { textoDetectado.contains(it) }

                    if (esCredencial) {
                        imagenUri = uri
                        credencialValida = true
                        binding.tvNombreArchivo.text = uri.lastPathSegment ?: "credencial_valida.jpg"
                        binding.ivPreviewCredencial.visibility = View.VISIBLE
                        binding.ivPreviewCredencial.load(uri)
                        Toast.makeText(requireContext(), "Credencial verificada correctamente", Toast.LENGTH_SHORT).show()
                    } else {
                        imagenUri = null
                        credencialValida = false
                        binding.tvNombreArchivo.text = "Archivo no válido"
                        binding.ivPreviewCredencial.visibility = View.GONE
                        Toast.makeText(
                            requireContext(),
                            "La imagen no parece ser una credencial universitaria. Sube una foto clara de tu credencial.",
                            Toast.LENGTH_LONG
                        ).show()
                    }
                }
                .addOnFailureListener {
                    Toast.makeText(requireContext(), "No se pudo analizar la imagen, intenta con otra foto", Toast.LENGTH_SHORT).show()
                }
        } catch (e: Exception) {
            Toast.makeText(requireContext(), "Error al leer imagen: ${e.message}", Toast.LENGTH_SHORT).show()
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}