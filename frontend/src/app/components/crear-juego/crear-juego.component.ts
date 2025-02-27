import { Component, OnInit } from '@angular/core';
import {
  FormBuilder,
  FormGroup,
  Validators,
  ReactiveFormsModule,
} from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { JuegoService } from '../../services/juego.service'; // Ajusta la ruta según tu estructura
import { Router } from '@angular/router';

@Component({
  selector: 'app-crear-juego',
  standalone: true,
  imports: [ReactiveFormsModule, HttpClientModule],
  templateUrl: './crear-juego.component.html',
  styleUrls: ['./crear-juego.component.css'],
})
export class CrearJuegoComponent implements OnInit {
  juegoForm: FormGroup;
  selectedJuegoFile: File | null = null;
  selectedLogoFile: File | null = null;

  constructor(
    private fb: FormBuilder,
    private juegoService: JuegoService,
    private router: Router
  ) {
    this.juegoForm = this.fb.group({
      titulo: ['', Validators.required],
      descripcion: [''],
      categoria: ['', Validators.required],
      // Nota: Los campos de archivo no se definen en el FormGroup, se manejarán por separado.
    });
  }

  ngOnInit(): void {}

  onFileSelected(event: any, type: 'juego' | 'logo'): void {
    const file: File = event.target.files[0];
    if (file) {
      if (type === 'juego') {
        this.selectedJuegoFile = file;
      } else if (type === 'logo') {
        this.selectedLogoFile = file;
      }
    }
  }

  submitJuego(): void {
    console.log('Submit llamado. Formulario válido:', this.juegoForm.valid);
    if (this.juegoForm.valid && this.selectedJuegoFile) {
      const formData = new FormData();
      // Agregar un usuario_id por defecto para pruebas:
      formData.append('usuario_id', '1');
      formData.append('titulo', this.juegoForm.get('titulo')?.value);
      formData.append('descripcion', this.juegoForm.get('descripcion')?.value);
      formData.append('categoria_id', this.juegoForm.get('categoria')?.value);
      // Agrega el archivo del juego
      formData.append('juegoFile', this.selectedJuegoFile);
      // Agrega el logo si se seleccionó
      if (this.selectedLogoFile) {
        formData.append('logoFile', this.selectedLogoFile);
      }

      console.log('Enviando datos:', formData);
      this.juegoService.crearJuego(formData).subscribe({
        next: (res) => {
          console.log('Juego subido exitosamente', res);
          // Redirige a la lista de juegos
          this.router.navigate(['/juegos']);
        },
        error: (err) => {
          console.error('Error al subir el juego', err);
        },
      });
    } else {
      console.error(
        'El formulario no es válido o no se seleccionó el archivo del juego.'
      );
    }
  }
}
