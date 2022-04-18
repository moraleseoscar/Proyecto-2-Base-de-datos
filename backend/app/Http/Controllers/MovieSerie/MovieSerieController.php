<?php

namespace App\Http\Controllers\MovieSerie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MovieSerie;
use Illuminate\Support\Facades\Validator;

class MovieSerieController extends Controller
{   
    public function index() {
        try {
            $movieSeries = MovieSerie::all();
            return response(["result" => 'success', 'data' => $movieSeries], 200);
        } catch (\Exception $e) {
            return response(['result' => 'fail', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request) {
        try {
            $data = $request->all();
            
            $mov_serie_validator = Validator::make($data, [
                'nombre' => 'required',
                'id_director' => 'required',
                'tipo' => 'required',
                'fecha_estreno' => 'required',
                'descripcion' => 'required',
                'duracion' => 'required',
                'link_video' => 'required'
            ]);
            
            if ($mov_serie_validator->fails()) {
                return response(['result' => 'fail', 'message' => $mov_serie_validator->errors()], 500);
            }

            /* $data['id_usuario'] = auth()->user()->id;
            $data['activo'] = true; */
            
            MovieSerie::create($data);
            
            return ['result' => 'success', "message"=> 'Movie_serie almacenada exitósamente.'];
        } catch (\Exception $e) {
            return response(['result' => 'fail', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id) {
        try {
            $movieSerie = \DB::select("SELECT ps.id,ps.nombre, ps.tipo, ps.fecha_estreno, ps.descripcion,ps.duracion,ps.link_video,ps.portada, d.nombre AS director
                                        FROM peliculas_series ps
                                        INNER JOIN director d ON d.id = ps.id_director 
                                        WHERE ps.id = $id"); 
            $actores = \DB::select("SELECT nombre FROM actores_peliculas INNER JOIN actores ON actores.id = actores_peliculas.id_actores WHERE id_pelicula = $id");
            $premios = \DB::select("SELECT premios.nombre FROM premios INNER JOIN peliculas_series ON premios.id_pelicula = peliculas_series.id WHERE peliculas_series.id = $id");
            $categorias = \DB::select("SELECT categorias.id, nombre FROM categoria_pelicula INNER JOIN categorias ON categorias.id = categoria_pelicula.id_categoria WHERE id_pelicula = $id");
            $categorias_id = "";
            foreach ($categorias as $variable) {
                $categorias_id = $categorias_id . strval($variable->id) . ", ";
            }
            $categorias_id = rtrim($categorias_id, ", ");
            $query = "SELECT DISTINCT peliculas_series.id, peliculas_series.nombre, peliculas_series.portada FROM peliculas_series 
            INNER JOIN categoria_pelicula ON categoria_pelicula.id_pelicula = peliculas_series.id 
            WHERE categoria_pelicula.id_categoria =  ANY(ARRAY[$categorias_id]) AND peliculas_series.id != $id LIMIT 5";

            $related = \DB::select($query);
            return response(["result" => 'success', "data" => ["related" => $related, "movie" => $movieSerie[0], "actors" => $actores, 'awards' => $premios, 'categories' => $categorias]], 200);
        } catch (\Exception $e) {
            return response(['result' => 'fail', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, MovieSerie $movieSerie) {
        try {
            $movieSerie->update($request->all());
            return response(["result" => "success", "message" => 'Movie_serie actualizada exitósamente.'], 200);
        } catch (\Exception $e) {
            return response(['result' => 'fail', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(MovieSerie $movieSerie) {
        try {
            $movieSerie->delete();
            return response(['result' => 'fail', 'message' => 'Favorito eliminada exitósamente.'], 500);
        } catch (\Exception $e) {
            return response(['result' => 'fail', 'message' => $e->getMessage()], 500);
        }
    }
}
