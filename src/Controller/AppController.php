<?php

namespace App\Controller;

use App\Entity\Weather;
use App\Repository\WeatherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;

class AppController extends AbstractController
{
    /**
     * @Route("/app", name="app")
     */
    public function getData(Request $request)
    {
        $isAjax = $request->isXMLHttpRequest();
        $city = $request->request->get('city');

        if (!$isAjax && !$city) {
            return new Response('Error');
        }

        $client = HttpClient::create();
        $api = 'https://api.openweathermap.org/data/2.5/forecast';
        $response = $client->request('GET', $api, [
            'query' => [
                'q' => $city,
                'appid' => '731fdb9f46272f54a8b68c894765410b',
            ],
        ]);

        $content = $response->toArray();

        $repository = $this->getDoctrine()->getRepository(Weather::class);
        $nowDate = new \DateTime('now');

        $weatherData = $repository->getWether($nowDate, $city);


        if (empty($weatherData) && !empty($content['list'])) {

            $entityManager = $this->getDoctrine()->getManager();

            foreach ($content['list'] as $item) {
                $weather = new Weather();
                $weather->setCity($content['city']['name']);
                $weather->setDate(\DateTime::createFromFormat('Y-m-d H:i:s', $item['dt_txt']));
                $weather->setHumidity($item['main']['humidity']);
                $weather->setPressure($item['main']['pressure']);
                $weather->setTemperature($item['main']['temp']);

                $entityManager->persist($weather);
            }

            $entityManager->flush();
            $weatherData = $repository->getWether($nowDate, $city);
        }

        $weather = $this->prepareData($weatherData);


        return new JsonResponse($weather);
    }

    protected function prepareData($weather)
    {
        $data = [];

        foreach ($weather as $key => $value) {
            $data[$key]['city'] = $value->getCity();
            $data[$key]['date'] = $value->getDate()->format('Y-m-d H:i:s');
            $data[$key]['temperature'] = $value->getTemperature();
            $data[$key]['humidity'] = $value->getHumidity();
            $data[$key]['pressure'] = $value->getPressure();
        }
        return $data;
    }

}
