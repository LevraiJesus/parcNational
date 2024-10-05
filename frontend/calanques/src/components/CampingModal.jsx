import React, { useState } from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';

const CampingModal = ({ isOpen, onClose, onCampingAdded }) => {
  const [formData, setFormData] = useState({
    name: '',
    longitude: '',
    latitude: '',
    description: '',
    price: '',
    capacity: ''
  });
  const [image, setImage] = useState(null);

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleImageChange = (e) => {
    setImage(e.target.files[0]);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    const data = new FormData();
    for (const key in formData) {
      data.append(key, formData[key]);
    }
    if (image) {
      data.append('image', image);
    }

    try {
      const response = await axios.post('http://localhost:8000/campings', data, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'multipart/form-data'
        }
      });
      onCampingAdded(response.data);
      onClose();
    } catch (error) {
      console.error('Error adding camping:', error);
    }
  };

  if (!isOpen) return null;

  return ReactDOM.createPortal(
    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[9999] flex items-center justify-center">
      <div className="relative p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 className="text-lg font-bold mb-4">Add New Camping</h3>
        <form onSubmit={handleSubmit}>
          <input type="text" name="name" value={formData.name} onChange={handleChange} placeholder="Name" className="w-full mb-2 p-2 border rounded" />
          <input type="number" step="0.000001" name="longitude" value={formData.longitude} onChange={handleChange} placeholder="Longitude" className="w-full mb-2 p-2 border rounded" />
          <input type="number" step="0.000001" name="latitude" value={formData.latitude} onChange={handleChange} placeholder="Latitude" className="w-full mb-2 p-2 border rounded" />
          <textarea name="description" value={formData.description} onChange={handleChange} placeholder="Description" className="w-full mb-2 p-2 border rounded"></textarea>
          <input type="number" step="0.01" name="price" value={formData.price} onChange={handleChange} placeholder="Price" className="w-full mb-2 p-2 border rounded" />
          <input type="number" name="capacity" value={formData.capacity} onChange={handleChange} placeholder="Capacity" className="w-full mb-2 p-2 border rounded" />
          <input type="file" name="image" onChange={handleImageChange} className="w-full mb-2 p-2 border rounded" />
          <div className="flex justify-end">
            <button type="submit" className="bg-blue-500 text-white px-4 py-2 rounded mr-2">Submit</button>
            <button onClick={onClose} className="bg-gray-300 px-4 py-2 rounded">Close</button>
          </div>
        </form>
      </div>
    </div>,
    document.body
  );
};

export default CampingModal;
