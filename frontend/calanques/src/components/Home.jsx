import { useEffect, useState } from 'react';
import { MapContainer, TileLayer, Marker, Popup } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import axios from 'axios';

import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
import iconUrl from 'leaflet/dist/images/marker-icon.png';
import shadowUrl from 'leaflet/dist/images/marker-shadow.png';

function Home({ user, setUser }) {
  const [campings, setCampings] = useState([]);
  const [trails, setTrails] = useState([]);

  useEffect(() => {
    delete L.Icon.Default.prototype._getIconUrl;
    L.Icon.Default.mergeOptions({
      iconRetinaUrl,
      iconUrl,
      shadowUrl,
    });

    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      const campingsResponse = await axios.get('http://localhost:8000/campings');
      const trailsResponse = await axios.get('http://localhost:8000/trails');
      console.log('Campings:', campingsResponse.data);
      console.log('Trails:', trailsResponse.data);
      setCampings(campingsResponse.data);
      setTrails(trailsResponse.data);
    } catch (error) {
      console.error('Error fetching data:', error);
    }
  };

  const calanquesCenter = [43.2167, 5.4000];

  return (
    <div className="flex flex-col items-center justify-center min-h-screen bg-gray-100">
      <h1 className="text-3xl font-bold mb-4">Welcome, {user.firstname} {user.name}!</h1>
      <div style={{ height: '500px', width: '100%' }}>
        <MapContainer center={calanquesCenter} zoom={10} style={{ height: '100%', width: '100%' }}>
          <TileLayer
            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
          />
          {campings.map((camping) => (
            <Marker key={camping.id} position={[camping.latitude, camping.longitude]}>
              <Popup>{camping.name}</Popup>
            </Marker>
          ))}
          {trails.map((trail) => (
            <Marker key={trail.id} position={[trail.latitudeStart, trail.longitudeStart]}>
              <Popup>{trail.name}</Popup>
            </Marker>
          ))}
        </MapContainer>
      </div>
    </div>
  );
}

export default Home;
