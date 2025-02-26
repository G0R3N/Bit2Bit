import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { API_CONFIG } from '../api.config';

@Injectable({
  providedIn: 'root'
})
export class JuegoService {

  //URL api
  private baseUrl = API_CONFIG.baseUrl;

  constructor(private http: HttpClient) { }

  //metodo para obtener los juegos
  obtenerJuegos(): Observable<any> {
    return this.http.get(`${this.baseUrl}getJuegos.php`);
  }

  //metodo crear los juegos
  crearJuego(juegoData: any): Observable<any> {
    return this.http.post(`${this.baseUrl}newJuego.php`, juegoData);
  }
}
