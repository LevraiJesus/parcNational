import { useEffect, useState } from 'react';
import { MapContainer, TileLayer, Marker, Popup, Polyline } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import axios from 'axios';
import TrailModal from './TrailModal';
import CampingModal from './CampingModal';

import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
import iconUrl from 'leaflet/dist/images/marker-icon.png';
import shadowUrl from 'leaflet/dist/images/marker-shadow.png';

function Home({ user, setUser }) {
  const [campings, setCampings] = useState([]);
  const [trails, setTrails] = useState([]);
  const [selectedItem, setSelectedItem] = useState(null);

  const [isTrailModalOpen, setIsTrailModalOpen] = useState(false);
  const [isCampingModalOpen, setIsCampingModalOpen] = useState(false);

  const openTrailModal = () => setIsTrailModalOpen(true);
  const closeTrailModal = () => setIsTrailModalOpen(false);
  const openCampingModal = () => setIsCampingModalOpen(true);
  const closeCampingModal = () => setIsCampingModalOpen(false);

  const handleTrailAdded = (newTrail) => {
    fetchTrails();
  };
  
  const handleCampingAdded = (newCamping) => {
    fetchCampings();
  };
  

  useEffect(() => {
    delete L.Icon.Default.prototype._getIconUrl;
    L.Icon.Default.mergeOptions({
      iconRetinaUrl,
      iconUrl,
      shadowUrl,
    });

    fetchCampings();
    fetchTrails();
  }, []);

  const fetchCampings = async () => {
    try {
      const response = await axios.get('http://localhost:8000/campings');
      setCampings(response.data);
    } catch (error) {
      console.error('Error fetching campings:', error);
    }
  };

  const fetchTrails = async () => {
    try {
      const response = await axios.get('http://localhost:8000/trails');
      setTrails(response.data);
    } catch (error) {
      console.error('Error fetching trails:', error);
    }
  };

  const calanquesCenter = [43.2167, 5.4000];

  const handleItemClick = (item) => {
    setSelectedItem(item);
  };

  const deleteItem = async (item, type) => {
    try {
      await axios.delete(`http://localhost:8000/${type}/${item.id}`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      });
      if (type === 'campings') {
        setCampings(campings.filter(camping => camping.id !== item.id));
      } else {
        setTrails(trails.filter(trail => trail.id !== item.id));
      }
      if (selectedItem && selectedItem.id === item.id) {
        setSelectedItem(null);
      }
    } catch (error) {
      console.error(`Error deleting ${type}:`, error);
    }
  };

  const ListItem = ({ item, type }) => (
    <div className="flex justify-between items-center cursor-pointer hover:bg-gray-200 p-2">
      <span onClick={() => handleItemClick(item)}>{item.name}</span>
      <button 
        onClick={(e) => {
          e.stopPropagation();
          deleteItem(item, type);
        }}
        className="text-red-500 hover:text-red-700"
      >
        &#10005;
      </button>
    </div>
  );
  const mapCenter = calanquesCenter;
  const mapZoom = 10;

  return (
    <div className="flex flex-col items-center justify-center min-h-screen bg-gray-100">
      <h1 className="text-3xl font-bold mb-4">Welcome, {user.firstname} {user.name}!</h1>
      <div className="flex w-full" style={{ height: '500px' }}>
        <div className="w-1/4 overflow-y-auto p-4 bg-white">
          <h2 className="text-xl font-bold mb-2">Campings</h2>
          <button onClick={openCampingModal} className="mb-2 bg-blue-500 text-white px-4 py-2 rounded">Add Camping</button>
          {campings.map((camping) => (
              <ListItem key={camping.id} item={camping} type="campings" />
            ))}
        </div>
        <div className="w-1/2">
          <MapContainer center={mapCenter} zoom={mapZoom} style={{ height: '100%', width: '100%' }}>
            <TileLayer
              url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
              attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            />
            {selectedItem && 'latitude' in selectedItem && (
              <Marker position={[selectedItem.latitude, selectedItem.longitude]}>
                <Popup>{selectedItem.name}</Popup>
              </Marker>
            )}
            {selectedItem && 'latitudeStart' in selectedItem && (
              <>
                <Marker position={[selectedItem.latitudeStart, selectedItem.longitudeStart]}>
                  <Popup>{selectedItem.name} - Start</Popup>
                </Marker>
                <Marker position={[selectedItem.latitudeEnd, selectedItem.longitudeEnd]}>
                  <Popup>{selectedItem.name} - End</Popup>
                </Marker>
                <Polyline positions={[
                  [selectedItem.latitudeStart, selectedItem.longitudeStart],
                  [selectedItem.latitudeEnd, selectedItem.longitudeEnd]
                ]} />
              </>
            )}
          </MapContainer>
        </div>
        <div className="w-1/4 overflow-y-auto p-4 bg-white">
          <h2 className="text-xl font-bold mb-2">Trails</h2>
          <button onClick={openTrailModal} className="mb-2 bg-blue-500 text-white px-4 py-2 rounded">Add Trail</button>
          {trails.map((trail) => (
              <ListItem key={trail.id} item={trail} type="trails" />
            ))}
        </div>
      </div>
      <TrailModal isOpen={isTrailModalOpen} onClose={closeTrailModal} onTrailAdded={handleTrailAdded} />
      <CampingModal isOpen={isCampingModalOpen} onClose={closeCampingModal} onCampingAdded={handleCampingAdded} />
    </div>
  );
}

export default Home;
