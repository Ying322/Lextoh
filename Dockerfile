##############################################################################
# Dockerfile to run Lextoh
# Based on php
#############################################################################
#
# Build part
#

FROM php:apache

LABEL maintainer="Mathieu.Mangeot@imag.fr"

WORKDIR /var/www/html

COPY . .
