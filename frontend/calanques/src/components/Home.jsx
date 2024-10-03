import { useNavigate } from 'react-router-dom';

function Home({ user, setUser }) {
  const navigate = useNavigate();

  const handleLogout = () => {
    localStorage.removeItem('token');
    setUser(null);
    navigate('/login');
  };

  return (
    <div className="flex flex-col items-center justify-center min-h-screen bg-gray-100">
      <h1 className="text-3xl font-bold mb-4">Welcome, {user.firstname} {user.name}!</h1>
      <p className="mb-2">Email: {user.email}</p>
      <p className="mb-2">Phone: {user.phoneNumber}</p>
      <p className="mb-4">Role: {user.admin ? 'Admin' : 'User'}</p>
      <button
        onClick={handleLogout}
        className="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
      >
        Logout
      </button>
    </div>
  );
}

export default Home;
