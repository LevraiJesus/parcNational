import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';

const TrailModal = ({ isOpen, onClose, onTrailAdded }) => {
  const [formData, setFormData] = useState({
    name: '',
    longitudeStart: '',
    longitudeEnd: '',
    latitudeStart: '',
    latitudeEnd: '',
    distance: '',
    heightDiff: '',
    difficulty: ''
  });

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const response = await axios.post('http://localhost:8000/trails', formData, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      });
      onTrailAdded(response.data);
      onClose();
    } catch (error) {
      console.error('Error adding trail:', error);
    }
  };

  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = 'unset';
    }
    return () => {
      document.body.style.overflow = 'unset';
    };
  }, [isOpen]);

  if (!isOpen) return null;

  return ReactDOM.createPortal(
    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[9999] flex items-center justify-center">

      <div className="relative p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 className="text-lg font-bold mb-4">Add New Trail</h3>
        <form onSubmit={handleSubmit}>
          <input type="text" name="name" value={formData.name} onChange={handleChange} placeholder="Name" className="w-full mb-2 p-2 border rounded" />
          <input type="number" step="0.000001" name="longitudeStart" value={formData.longitudeStart} onChange={handleChange} placeholder="Longitude Start" className="w-full mb-2 p-2 border rounded" />
          <input type="number" step="0.000001" name="longitudeEnd" value={formData.longitudeEnd} onChange={handleChange} placeholder="Longitude End" className="w-full mb-2 p-2 border rounded" />
          <input type="number" step="0.000001" name="latitudeStart" value={formData.latitudeStart} onChange={handleChange} placeholder="Latitude Start" className="w-full mb-2 p-2 border rounded" />
          <input type="number" step="0.000001" name="latitudeEnd" value={formData.latitudeEnd} onChange={handleChange} placeholder="Latitude End" className="w-full mb-2 p-2 border rounded" />
          <input type="number" step="0.1" name="distance" value={formData.distance} onChange={handleChange} placeholder="Distance" className="w-full mb-2 p-2 border rounded" />
          <input type="number" step="0.1" name="heightDiff" value={formData.heightDiff} onChange={handleChange} placeholder="Height Difference" className="w-full mb-2 p-2 border rounded" />
          <input type="text" name="difficulty" value={formData.difficulty} onChange={handleChange} placeholder="Difficulty" className="w-full mb-2 p-2 border rounded" />
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

export default TrailModal;
