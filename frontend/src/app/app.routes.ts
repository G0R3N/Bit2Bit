import { Routes } from '@angular/router';
import { ListadoJuegosComponent } from './components/listado-juegos/listado-juegos.component';

export const routes: Routes = [
  { path: '', component: ListadoJuegosComponent },
  { path: '**', redirectTo: '' }
];
