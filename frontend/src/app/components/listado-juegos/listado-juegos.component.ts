import { Component, OnInit } from '@angular/core';
import { JuegoService } from '../../services/juego.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-listado-juegos',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './listado-juegos.component.html',
  styleUrls: ['./listado-juegos.component.css'],
})
export class ListadoJuegosComponent implements OnInit {
  juegos: any[] = [];

  constructor(private juegoService: JuegoService) {}

  ngOnInit(): void {
    this.juegoService.obtenerJuegos().subscribe((response) => {
      if (response.success) {
        this.juegos = response.data;
      } else {
        console.error('Error al obtener juegos:', response.error);
      }
    });
  }
}
