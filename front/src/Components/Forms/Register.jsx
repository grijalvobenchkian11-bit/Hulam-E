import React, { useState, useEffect } from "react";
import "./Register.css";
import axios from "axios";
import { EyeIcon, EyeSlashIcon } from "@heroicons/react/24/solid";
import { Link, useNavigate } from "react-router-dom";
import TermsModal from "../PrivacyPolicy/TermsModal";
import AlertMessage from "../Common/AlertMessage";

const API_URL = process.env.REACT_APP_API_URL;

const Register = () => {
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    acceptTerms: false,
  });

  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);
  const [alert, setAlert] = useState({ show: false, type: "", message: "" });
  const [fieldErrors, setFieldErrors] = useState({});
  const [showTermsModal, setShowTermsModal] = useState(false);
  const navigate = useNavigate();

  const showAlert = (type, message) => {
    setAlert({ show: true, type, message });
    setTimeout(() => setAlert({ show: false, type: "", message: "" }), 5000);
  };

  const togglePasswordVisibility = () => {
    setShowPassword(!showPassword);
  };

  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: type === "checkbox" ? checked : value,
    }));

    if (fieldErrors[name]) {
      setFieldErrors((prev) => ({ ...prev, [name]: undefined }));
    }
  };

  const validateForm = () => {
    const errors = {};
    if (!formData.name.trim()) errors.name = "Name is required";
    if (!formData.email.trim()) errors.email = "Email is required";
    if (!formData.password) errors.password = "Password is required";
    if (formData.password !== formData.password_confirmation)
      errors.password_confirmation = "Passwords do not match";
    if (!formData.acceptTerms)
      errors.acceptTerms = "You must accept the terms";

    setFieldErrors(errors);
    return Object.keys(errors).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!validateForm()) {
      showAlert("error", "Please fix the errors below.");
      return;
    }

    setLoading(true);
    setAlert({ show: false, type: "", message: "" });

    try {
      const response = await axios.post(
        `${API_URL}/register`,
        {
          name: formData.name.trim(),
          email: formData.email.trim().toLowerCase(),
          password: formData.password,
          password_confirmation: formData.password_confirmation,
        },
        {
          headers: {
            "Content-Type": "application/json",
          },
          withCredentials: false, // 🚫 IMPORTANT
        }
      );

      showAlert("success", "Registration successful! Redirecting...");
      setTimeout(() => navigate("/rental-section"), 1500);
    } catch (error) {
      console.error("REGISTER ERROR:", error);

      if (!error.response) {
        showAlert("error", "Network error. Backend unreachable.");
      } else if (error.response.status === 422) {
        setFieldErrors(error.response.data.errors || {});
        showAlert("error", "Validation failed.");
      } else {
        showAlert("error", "Registration failed. Please try again.");
      }
    } finally {
      setLoading(false);
    }
  };

  const handleTermsAcceptance = () => {
    setFormData({ ...formData, acceptTerms: true });
    setShowTermsModal(false);
  };

  return (
    <main className="register-page">
      <section className="register-left">
        <p className="register-greetings">Hello, Welcome!</p>
        <p className="register-question">Already have an account?</p>
        <Link to="/login">
          <button className="register-login-btn">Login</button>
        </Link>
      </section>

      <section className="register-form">
        <p className="register-title">Register</p>

        {alert.show && (
          <AlertMessage
            type={alert.type}
            message={alert.message}
            onClose={() => setAlert({ show: false, type: "", message: "" })}
          />
        )}

        <form onSubmit={handleSubmit}>
          <input name="name" placeholder="Full Name" onChange={handleChange} />
          <input name="email" placeholder="Email" onChange={handleChange} />
          <input
            type={showPassword ? "text" : "password"}
            name="password"
            placeholder="Password"
            onChange={handleChange}
          />
          <input
            type={showPassword ? "text" : "password"}
            name="password_confirmation"
            placeholder="Confirm Password"
            onChange={handleChange}
          />

          <label>
            <input
              type="checkbox"
              checked={formData.acceptTerms}
              onChange={() => setShowTermsModal(true)}
            />
            I agree to Terms
          </label>

          <button disabled={loading}>
            {loading ? "Creating Account..." : "Register"}
          </button>
        </form>
      </section>

      {showTermsModal && (
        <TermsModal
          onClose={() => setShowTermsModal(false)}
          onAccept={handleTermsAcceptance}
        />
      )}
    </main>
  );
};

export default Register;
