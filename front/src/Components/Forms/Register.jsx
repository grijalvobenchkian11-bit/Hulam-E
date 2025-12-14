import React, { useState, useEffect } from "react";
import "./Register.css";
import { EyeIcon, EyeSlashIcon } from "@heroicons/react/24/solid";
import { Link, useNavigate } from "react-router-dom";
import TermsModal from "../PrivacyPolicy/TermsModal";
import AlertMessage from "../Common/AlertMessage";

// ✅ IMPORT YOUR CENTRAL API
import { authAPI } from "../../services/api";

const Register = () => {
  const navigate = useNavigate();

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

  const showAlert = (type, message) => {
    setAlert({ show: true, type, message });
    setTimeout(() => {
      setAlert({ show: false, type: "", message: "" });
    }, 5000);
  };

  const togglePasswordVisibility = () => {
    setShowPassword((prev) => !prev);
  };

  const validateForm = () => {
    const errors = {};

    if (!formData.name.trim()) errors.name = "Full name is required";
    if (!formData.email.trim()) errors.email = "Email is required";
    if (!formData.password) errors.password = "Password is required";
    if (formData.password !== formData.password_confirmation) {
      errors.password_confirmation = "Passwords do not match";
    }
    if (!formData.acceptTerms) {
      errors.acceptTerms = "You must accept the terms and conditions";
    }

    setFieldErrors(errors);
    return Object.keys(errors).length === 0;
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

    if (alert.show) {
      setAlert({ show: false, type: "", message: "" });
    }
  };

  // ✅ FIXED SUBMIT HANDLER
  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!validateForm()) {
      showAlert("error", "Please correct the errors below.");
      return;
    }

    setLoading(true);

    try {
      await authAPI.register({
        name: formData.name.trim(),
        email: formData.email.trim().toLowerCase(),
        password: formData.password,
        password_confirmation: formData.password_confirmation,
      });

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
        showAlert(
          "error",
          error.response?.data?.message || "Registration failed."
        );
      }
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    const handleClick = () => {
      if (alert.show) {
        setAlert({ show: false, type: "", message: "" });
      }
    };

    document.addEventListener("click", handleClick);
    return () => document.removeEventListener("click", handleClick);
  }, [alert.show]);

  const handleTermsAcceptance = () => {
    setFormData((prev) => ({ ...prev, acceptTerms: true }));
    setShowTermsModal(false);
    if (fieldErrors.acceptTerms) {
      setFieldErrors((prev) => ({ ...prev, acceptTerms: undefined }));
    }
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
          <div className="register-formFields">
            <div className="register-input-container">
              <input
                type="text"
                name="name"
                value={formData.name}
                onChange={handleChange}
                placeholder="Full Name"
                className={fieldErrors.name ? "error" : ""}
                disabled={loading}
              />
              {fieldErrors.name && (
                <p className="register-field-error">{fieldErrors.name}</p>
              )}
            </div>

            <div className="register-input-container">
              <input
                type="email"
                name="email"
                value={formData.email}
                onChange={handleChange}
                placeholder="Email"
                className={fieldErrors.email ? "error" : ""}
                disabled={loading}
              />
              {fieldErrors.email && (
                <p className="register-field-error">{fieldErrors.email}</p>
              )}
            </div>

            <div className="register-input-container">
              <div className="register-pass">
                <input
                  type={showPassword ? "text" : "password"}
                  name="password"
                  value={formData.password}
                  onChange={handleChange}
                  placeholder="Password"
                  className={fieldErrors.password ? "error" : ""}
                  disabled={loading}
                />
                <button
                  type="button"
                  onClick={togglePasswordVisibility}
                  disabled={loading}
                >
                  {showPassword ? <EyeSlashIcon /> : <EyeIcon />}
                </button>
              </div>
              {fieldErrors.password && (
                <p className="register-field-error">{fieldErrors.password}</p>
              )}
            </div>

            <div className="register-input-container">
              <input
                type={showPassword ? "text" : "password"}
                name="password_confirmation"
                value={formData.password_confirmation}
                onChange={handleChange}
                placeholder="Confirm Password"
                className={
                  fieldErrors.password_confirmation ? "error" : ""
                }
                disabled={loading}
              />
              {fieldErrors.password_confirmation && (
                <p className="register-field-error">
                  {fieldErrors.password_confirmation}
                </p>
              )}
            </div>
          </div>

          <div className="register-terms-container">
            <label>
              <input
                type="checkbox"
                checked={formData.acceptTerms}
                onChange={(e) => {
                  if (e.target.checked) {
                    e.preventDefault();
                    setShowTermsModal(true);
                  } else {
                    setFormData((prev) => ({
                      ...prev,
                      acceptTerms: false,
                    }));
                  }
                }}
                disabled={loading}
              />
              I agree to the Terms and Conditions
            </label>
            {fieldErrors.acceptTerms && (
              <p className="register-field-error">
                {fieldErrors.acceptTerms}
              </p>
            )}
          </div>

          <button type="submit" disabled={loading}>
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
