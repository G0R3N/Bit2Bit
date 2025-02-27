import { Routes } from '@angular/router';
import { ListadoJuegosComponent } from './components/listado-juegos/listado-juegos.component';
import { CrearJuegoComponent } from './components/crear-juego/crear-juego.component';

export const routes: Routes = [
  { path: '', component: ListadoJuegosComponent },
  { path: 'crear-juego', component:CrearJuegoComponent},
  { path: '**', redirectTo: '' },
];
