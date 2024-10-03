import { useState, useEffect } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import axios from 'axios';

function Signup({ setUser }) {
  const [formData, setFormData] = useState({
    email: '',
    password: '',
    confirmPassword: '',
    name: '',
    firstname: '',
    phoneNumber: '',
  });
  const [errors, setErrors] = useState({});
  const navigate = useNavigate();

  const validateField = (name, value) => {
    switch (name) {
      case 'email':
        return !value ? 'Email is required' : !/\S+@\S+\.\S+/.test(value) ? 'Email is invalid' : '';
      case 'password':
        return !value ? 'Password is required' : value.length < 3 ? 'Password must be at least 3 characters' : '';
      case 'confirmPassword':
        return value !== formData.password ? 'Passwords do not match' : '';
      case 'name':
      case 'firstname':
        return !value ? `${name.charAt(0).toUpperCase() + name.slice(1)} is required` : '';
      case 'phoneNumber':
        return !value ? 'Phone number is required' : !/^\d{10}$/.test(value) ? 'Phone number must be 10 digits' : '';
      default:
        return '';
    }
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
    setErrors({ ...errors, [name]: validateField(name, value) });
  };

  useEffect(() => {
    if (formData.confirmPassword) {
      setErrors(prev => ({
        ...prev,
        confirmPassword: validateField('confirmPassword', formData.confirmPassword)
      }));
    }
  }, [formData.password, formData.confirmPassword]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    const newErrors = {};
    Object.keys(formData).forEach(key => {
      const error = validateField(key, formData[key]);
      if (error) newErrors[key] = error;
    });
  
    if (Object.keys(newErrors).length === 0) {
      try {
        const { confirmPassword, ...dataToSend } = formData;
        const response = await axios.post('http://localhost:8000/users', dataToSend);
        // Instead of setting the user and token, we'll navigate to the login page
        navigate('/login');
      } catch (error) {
        console.error('Signup failed:', error);
        setErrors({ submit: 'Signup failed. Please try again.' });
      }
    } else {
      setErrors(newErrors);
    }
  };
  

  return (
    <div className="flex items-center justify-center min-h-screen">
      <div className="w-full max-w-md">
        <form onSubmit={handleSubmit} className="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
          <h2 className="text-2xl mb-6 text-center font-bold">Sign Up</h2>
          {errors.submit && <p className="text-red-500 text-xs italic mb-4">{errors.submit}</p>}
          {Object.keys(formData).map((field) => (
            <div key={field} className="mb-4">
              <label className="block text-gray-700 text-sm font-bold mb-2" htmlFor={field}>
                {field.charAt(0).toUpperCase() + field.slice(1).replace(/([A-Z])/g, ' $1').trim()}
              </label>
              <input
                className={`shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline ${errors[field] ? 'border-red-500' : ''}`}
                id={field}
                type={field === 'password' || field === 'confirmPassword' ? 'password' : field === 'email' ? 'email' : 'text'}
                placeholder={field.charAt(0).toUpperCase() + field.slice(1).replace(/([A-Z])/g, ' $1').trim()}
                name={field}
                value={formData[field]}
                onChange={handleChange}
                />
              {errors[field] && <p className="text-red-500 text-xs italic">{errors[field]}</p>}
            </div>
          ))}
          <div className="flex items-center justify-between">
            <button
              className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
              type="submit"
            >
              Sign Up
            </button>
            <Link to="/login" className="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
              Login
            </Link>
          </div>
        </form>
      </div>
    </div>
  );
}

export default Signup;
