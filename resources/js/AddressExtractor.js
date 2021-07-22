export class AddressExtractor {
  /**
   * Format a Google Maps place
   */
  static format(components) {
    return {
      street_name: AddressExtractor.extract(components, 'route'),
      street_number: AddressExtractor.extract(components, 'street_number'),
      postal_code: AddressExtractor.extract(components, 'postal_code'),
      city: AddressExtractor.extract(components, 'locality'),
      country: AddressExtractor.extract(components, 'country'),
    }
  }

  /**
   * Extract an address component from the components array
   */
  static extract(components, type) {
    for (const component of components) {
      if (component.types.includes(type)) {
        return component.long_name;
      }
    }

    return null;
  }
}
