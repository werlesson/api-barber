<?php

namespace App\Http\Controllers;

// use Illuminate\Support\Facades\Auth;

use App\Models\Barber;
use App\Models\BarberAvailability;
use App\Models\BarberPhotos;
use App\Models\BarberServices;
use App\Models\BarberTestimonials;
use Illuminate\Http\Request;

class BarberController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');

        $this->loggedUser = auth()->user();
    }
    public function createRandom()
    {
        try {
            $array = ['error' => ''];

            for ($q = 0; $q < 15; $q++) {
                $names = ['Mario', 'Luigi', 'Yoshi', 'Bowser', 'Koopa', 'Peach', 'Daisy', 'Wario', 'Waluigi', 'Donkey Kong'];
                $lastNames = ['Mario', 'Luigi', 'Yoshi', 'Bowser', 'Koopa', 'Peach', 'Daisy', 'Wario', 'Waluigi', 'Donkey Kong'];
                $services = ['Corte', 'Pintura', 'Manicure', 'Pedicure', 'Barba', 'Sobrancelha'];
                $testimonials = [
                    "Ótimo atendimento! Fiquei muito satisfeito com o resultado.",
                    "Profissionais qualificados e ambiente agradável. Recomendo!",
                    "Sempre saio do salão com o visual renovado. Excelente serviço!",
                    "Atendimento rápido e eficiente. Com certeza voltarei mais vezes.",
                    "A equipe é muito atenciosa e os serviços são de alta qualidade.",
                    "Adorei o corte de cabelo! Fizeram exatamente como eu queria.",
                    "Os produtos utilizados são de primeira linha. Muito satisfeito!",
                    "Ambiente moderno e descontraído. Sempre me sinto bem-vindo.",
                    "Recomendo o salão para todos que buscam um ótimo atendimento.",
                    "Excelente opção para cuidar da barba. Resultado impecável!"
                ];

                $newBarber = new Barber();
                $newBarber->name = $names[array_rand($names)] . '' . $lastNames[array_rand($lastNames)];
                $newBarber->avatar = rand(1, 4) . '.png';
                $newBarber->stars = rand(2, 4) . '.' . rand(0, 9);
                $newBarber->latitude = '-23.5' . rand(0, 9) . '30907';
                $newBarber->longitude = '-46.6' . rand(0, 9) . '82795';
                $newBarber->save();

                $ns = rand(3, 6);

                for ($w = 0; $w < 4; $w++) {
                    $newBarberPhoto = new BarberPhotos();
                    $newBarberPhoto->id_barber = $newBarber->id;
                    $newBarberPhoto->url = rand(1, 5) . '.png';
                    $newBarberPhoto->save();
                }

                for ($w = 0; $w < $ns; $w++) {
                    $newBarberService = new BarberServices();
                    $newBarberService->id_barber = $newBarber->id;
                    $newBarberService->name = $services[array_rand($services)];
                    $newBarberService->price = rand(1, 99) . '.' . rand(0, 99);
                    $newBarberService->save();
                }

                for ($w = 0; $w < 5; $w++) {
                    $newBarberTestimonial = new BarberTestimonials();
                    $newBarberTestimonial->id_barber = $newBarber->id;
                    $newBarberTestimonial->name = $names[array_rand($names)];
                    $newBarberTestimonial->rate = rand(2, 4) . '.' . rand(0, 9);
                    $newBarberTestimonial->body = $testimonials[array_rand($testimonials)];
                    $newBarberTestimonial->save();
                }

                for ($i = 0; $i < 4; $i++) {
                    $rAdd = rand(7, 10);
                    $hours = [];
                    for ($r = 0; $r < 8; $r++) {
                        $time = $r + $rAdd;
                        if ($time < 10) {
                            $time = '0' . $time;
                        }
                        $hours[] = $time . ':00';
                    }

                    $newBarberAvailability = new BarberAvailability();
                    $newBarberAvailability->id_barber = $newBarber->id;
                    $newBarberAvailability->weekday = $i;
                    $newBarberAvailability->hours = implode(',', $hours);
                    $newBarberAvailability->save();
                }
            }
        } catch (\Throwable $th) {
            $array = ['error' => $th->getMessage()];
            return $array;
        }
    }

    public function list(Request $request)
    {
        try {
            $array = ['error' => ''];

            $barbers = Barber::all();

            foreach ($barbers as $bkey => $bvalue) {
                $barbers[$bkey]['avatar'] = url('media/avatars/' . $barbers[$bkey]['avatar']);
            }

            $array['data'] = $barbers;
            $array['loc'] = 'São Paulo';
            $array['quantidade'] = $barbers->count();

            return $array;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
