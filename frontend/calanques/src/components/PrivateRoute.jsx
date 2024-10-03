import { Navigate } from 'react-router-dom';

function PrivateRoute({ user, children }) {
  if (!user) {
    // If there's no user, redirect to the login page
    return <Navigate to="/login" replace />;
  }

  // If there is a user, render the child components
  return children;
}

export default PrivateRoute;
