# Landing Pages Plugin

This plugin does several things:

1. Creates Custom Post types for: City, Content, FAQs
2. Sets up a Landing Page template (single-city.php) that discovers all taxonomy terms registered to Content, creates an HTML block for each one and randomly selects a post of type content in that taxonomy, and puts that content in the section.
3. It also adds an FAQ section with a specific number of FAQs allowed.
4. It then randomizes the order of the sections
5. Then, getting the city, state, and phone metadata from the City post type, replaces any placeholders ( {_city}, {_state}, {_phone}) with the data from the metadata.
6. It also checks for a Universal Content section, which, if enabled, would output the same single block on the top of every landing page.
7. It allows for the import of a csv file with city, state, and phone data and and generates a City post with that data
8. It creates archive pages to show a full list of cities, content, and faqs
