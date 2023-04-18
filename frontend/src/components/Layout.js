import React from "react";
import PropTypes from "prop-types";
import { useTranslation } from "react-i18next";
import { Helmet } from "react-helmet";
import { Header } from "./Header";
import { Footer } from "./Footer";

export const Layout = ({ children }) => {
  const { t } = useTranslation("Common");

  return (
    <>
      <div className="background-container" />
      <Helmet>
        <title>{t("ApplicationName")}</title>
      </Helmet>
      <Header />
      <main role="main" className="content">
        {children}
      </main>
      <Footer />
    </>
  );
}

Layout.propTypes = {
  children: PropTypes.oneOfType([
    PropTypes.arrayOf(PropTypes.node),
    PropTypes.node,
  ]).isRequired,
};
