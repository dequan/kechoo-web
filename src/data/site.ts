export const site = {
  name: 'Band Saw Blade Supply',
  shortName: 'Band Saw Blade Supply',
  tagline: 'Bandsaw blades quoted by specification.',
  description:
    'Band Saw Blade Supply helps buyers compare hardened, bi-metal and carbide bandsaw blades for food and bone, wood, and metal cutting applications.',
  url: 'https://bandsawbladesupply.com',
  email: 'info@bandsawbladesupply.com',
  address: 'China manufacturing and export supply',
  defaultImage: '/images/hero-bandsaw-blade.png'
};

export const navigation = [
  { label: 'Products', href: '/products/' },
  { label: 'Applications', href: '/applications/' },
  { label: 'Technology', href: '/technologies/' },
  { label: 'Resources', href: '/resources/' },
  { label: 'Distributors', href: '/distributors/' },
  { label: 'About', href: '/about/' }
];

export const applications = [
  {
    slug: 'food-bone',
    title: 'Food & Bone',
    eyebrow: 'Butcher shops, meat processors, food plants',
    image: '/images/application-food-bone.png',
    alt: 'Bandsaw cutting frozen meat and bone in a hygienic processing room',
    description:
      'Hardened bandsaw blades for clean, repeatable cutting of fresh meat, frozen meat, poultry, fish and bone sections.',
    scene:
      'A butcher shop or meat plant needs replacement blades that fit the machine, cut cleanly through cold product and arrive in practical pack quantities.',
    buyerIntent:
      'Most buyers in this category already know the machine or old blade size. Confirm the dimensions and send a complete quotation request for a faster reply.',
    materials: ['Fresh meat', 'Frozen meat', 'Poultry', 'Fish', 'Small to medium bone sections'],
    problems: [
      'Wrong loop length prevents installation',
      'Too coarse a tooth pitch can tear softer product',
      'Poor weld finish can mark the product or shorten blade life',
      'Low-quality packing can create rust or edge damage in transit'
    ],
    quoteDetails: [
      'Measured blade length, width and thickness',
      'Machine brand or model if available',
      'Fresh or frozen product and bone size',
      'Pack quantity, destination country and reorder frequency'
    ],
    keywords: [
      'meat band saw blade',
      'bone saw blade',
      'frozen meat bandsaw blade',
      'butcher bandsaw blade',
      'meat cutting bandsaw blade supplier',
      'food processing band saw blade'
    ],
    buyerQuestions: [
      'Blade length, width and thickness from the machine label',
      'Fresh or frozen product and bone size',
      'Required pack quantity and destination country'
    ],
    faqs: [
      {
        question: 'Can Band Saw Blade Supply quote food and bone blades before order confirmation?',
        answer:
          'Yes. Send the blade size, machine information and quantity. Band Saw Blade Supply confirms fit, MOQ, packing and lead time before quotation.'
      },
      {
        question: 'What size information matters most for butcher band saw blades?',
        answer:
          'Loop length, blade width, thickness and tooth pitch are the most important values. A photo of the old blade package or machine plate helps avoid mistakes.'
      }
    ]
  },
  {
    slug: 'wood',
    title: 'Wood',
    eyebrow: 'Sawmills, resawing, furniture production',
    image: '/images/application-wood.png',
    alt: 'Industrial bandsaw cutting a solid timber beam',
    description:
      'High-carbon hardened blades for general woodworking, ripping, contour cutting and workshop replacement sizes.',
    scene:
      'A woodworking shop needs a blade that clears chips, tracks steadily and matches the wheel size of the machine without forcing a premium metal-cutting blade into the wrong job.',
    buyerIntent:
      'Buyers often compare replacement blade sizes, TPI and use cases such as hardwood, softwood, resawing or furniture production.',
    materials: ['Softwood', 'Hardwood', 'Boards', 'Furniture parts', 'General workshop stock'],
    problems: [
      'Wrong width can reduce tracking stability or fail to fit the machine',
      'Tooth pitch that is too fine can clog in thick wood',
      'Tooth pitch that is too coarse can leave a rougher finish',
      'Unclear machine size makes reorder quotations slow'
    ],
    quoteDetails: [
      'Machine model or measured loop length',
      'Wood type and approximate workpiece thickness',
      'Cut priority: finish, speed, blade life or general purpose',
      'Quantity per order and packaging preference'
    ],
    keywords: [
      'wood bandsaw blade',
      'sawmill band saw blade',
      'resaw bandsaw blade',
      'wood cutting blade supplier',
      'hardwood bandsaw blade',
      'woodworking band saw blade replacement'
    ],
    buyerQuestions: [
      'Machine model or measured blade loop length',
      'Softwood, hardwood, board thickness or log size',
      'Cut priority: finish, speed, blade life or general purpose'
    ],
    faqs: [
      {
        question: 'Can one wood blade cover both softwood and hardwood?',
        answer:
          'A general-purpose specification can cover many workshop jobs, but thicker hardwood or resawing may need a different width or pitch.'
      },
      {
        question: 'What should a wood buyer send for a quote?',
        answer:
          'Send loop length, width, thickness, tooth pitch if known, wood type, workpiece thickness and expected quantity.'
      }
    ]
  },
  {
    slug: 'metal',
    title: 'Metal',
    eyebrow: 'Fabrication shops, steel centers, production cutting',
    image: '/images/application-metal.png',
    alt: 'Horizontal bandsaw cutting rectangular steel tube',
    description:
      'Bi-metal and carbide bandsaw blades for carbon steel, alloy steel, tube, profiles, solids and demanding materials.',
    scene:
      'A metal fabrication shop needs a blade matched to material grade, section size, machine rigidity and daily cutting volume.',
    buyerIntent:
      'Buyers may know the material and section size before they know the exact blade. Workpiece data helps us recommend a quote-ready specification.',
    materials: ['Carbon steel', 'Alloy steel', 'Tube and profile', 'Bundles', 'High-alloy and abrasive metals'],
    problems: [
      'Too fine a pitch can overload teeth in large solid sections',
      'Too coarse a pitch can catch on thin-wall tube',
      'Weak machine rigidity can waste a premium carbide blade',
      'Coolant and feed conditions affect blade life as much as the SKU'
    ],
    quoteDetails: [
      'Material grade and workpiece shape',
      'Solid, tube, profile or bundle dimensions',
      'Machine model, wheel size and coolant condition',
      'Daily volume and target priority: speed, finish or blade life'
    ],
    keywords: [
      'metal cutting bandsaw blade',
      'M42 bimetal bandsaw blade',
      'carbide tipped bandsaw blade',
      'steel cutting blade supplier',
      'horizontal bandsaw blade for steel',
      'variable TPI metal bandsaw blade'
    ],
    buyerQuestions: [
      'Material grade and workpiece shape',
      'Solid, tube, profile or bundle dimensions',
      'Machine rigidity, coolant condition and expected volume'
    ],
    faqs: [
      {
        question: 'When should a buyer choose bi-metal instead of carbide?',
        answer:
          'Bi-metal is the practical starting point for many carbon steel, alloy steel, tube and profile jobs. Carbide is usually reviewed for rigid machines, abrasive materials and demanding production.'
      },
      {
        question: 'Why does Band Saw Blade Supply ask for section size before quoting metal blades?',
        answer:
          'Section size affects tooth pitch. Thin-wall tube, mixed profiles and large solids often need different variable-pitch choices.'
      }
    ]
  }
];

export const technologies = [
  {
    slug: 'hardened',
    title: 'Hardened',
    description:
      'High-carbon steel blades with induction-hardened teeth for food, bone, wood and general-purpose cutting.',
    bestFor: 'Food processing, butcher machines, woodworking and compact replacement sizes',
    buyingNotes: [
      'A practical choice when the workpiece is not demanding enough for bi-metal or carbide.',
      'Best quoted with exact blade loop dimensions and pack quantity.',
      'Often selected for replacement programs where reliable fit matters more than a listed unit price.'
    ],
    keywords: ['hardened bandsaw blade', 'high carbon bandsaw blade', 'induction hardened blade']
  },
  {
    slug: 'bi-metal',
    title: 'Bi-Metal',
    description:
      'Flexible alloy backing joined to a hardened high-speed steel tooth edge for reliable metal production cutting.',
    bestFor: 'Carbon steel, alloy steel, tube, profiles, bundles and mixed fabrication work',
    buyingNotes: [
      'A common starting point for metal-cutting buyers who need durable welded loops.',
      'Variable pitch helps control vibration across tube, profile and mixed sections.',
      'Quote accuracy improves when buyers send material grade and cross-section dimensions.'
    ],
    keywords: ['bi metal bandsaw blade', 'M42 bandsaw blade', 'metal bandsaw blade supplier']
  },
  {
    slug: 'carbide',
    title: 'Carbide',
    description:
      'Carbide-tipped blades for rigid machines, large sections, abrasive materials and high-alloy cutting demands.',
    bestFor: 'High-alloy steel, nickel alloy, abrasive materials and large solid sections',
    buyingNotes: [
      'Review machine rigidity before choosing carbide.',
      'Useful when bi-metal blade life is not enough for abrasive or high-alloy material.',
      'Best quoted with machine model, material grade, section size and daily cutting volume.'
    ],
    keywords: ['carbide bandsaw blade', 'carbide tipped band saw blade', 'high alloy cutting blade']
  }
];

export const resources = [
  {
    slug: 'how-to-measure-a-bandsaw-blade',
    title: 'How to measure a bandsaw blade before requesting a quote',
    description:
      'Measure blade length, width, thickness and tooth pitch so the supplier can confirm compatibility before quotation.',
    category: 'Selection guide',
    date: '2026-06-24',
    intro:
      'Most wrong quotations start with incomplete size data. Measure the old blade or machine plate first, then send the values in one message.',
    sections: [
      {
        heading: 'Measure loop length',
        body:
          'If the old blade is complete, measure the full welded loop. If the blade is broken, check the machine label, manual or previous packaging. Confirm whether the value is in millimeters or inches.'
      },
      {
        heading: 'Record width and thickness',
        body:
          'Blade width affects machine fit and cutting stability. Thickness must match the machine design. Do not estimate these values from a photo when a caliper or label is available.'
      },
      {
        heading: 'Confirm tooth pitch',
        body:
          'Tooth pitch is usually written as TPI or variable TPI, such as 4 TPI, 6/10 TPI or 3/4 TPI. If you cannot identify it, send a close photo of the teeth with a ruler.'
      }
    ],
    checklist: ['Loop length', 'Width', 'Thickness', 'TPI', 'Machine model', 'Photo of old blade or package'],
    relatedQueries: ['how to measure bandsaw blade length', 'bandsaw blade size guide', 'band saw blade replacement size']
  },
  {
    slug: 'choose-tpi-for-metal-cutting',
    title: 'How to choose TPI for metal cutting bandsaw blades',
    description:
      'Match tooth pitch to tube, profile and solid steel sections to reduce vibration and improve blade life.',
    category: 'Metal cutting',
    date: '2026-06-26',
    intro:
      'Metal cutting blade life depends heavily on tooth engagement. The material shape and section size matter as much as the blade material.',
    sections: [
      {
        heading: 'Thin-wall tube and profiles',
        body:
          'Finer or variable pitch helps reduce catching and vibration on thin-wall tube, mixed profiles and bundles. Send wall thickness and profile size before asking for a direct replacement.'
      },
      {
        heading: 'Solid steel sections',
        body:
          'Larger solids need enough tooth space to carry chips. Too fine a pitch can overload teeth, heat the cut and shorten blade life.'
      },
      {
        heading: 'Mixed production',
        body:
          'For fabrication shops cutting many shapes, send the full range of materials and dimensions. We can suggest a practical general-purpose variable pitch or split the job into two blade choices.'
      }
    ],
    checklist: ['Material grade', 'Tube, profile, solid or bundle', 'Largest section', 'Smallest section', 'Machine model', 'Coolant condition'],
    relatedQueries: ['metal bandsaw blade TPI chart', '6/10 TPI bandsaw blade', '3/4 TPI metal cutting blade']
  },
  {
    slug: 'food-bone-blade-buying-checklist',
    title: 'Food and bone bandsaw blade buying checklist',
    description:
      'A practical checklist for butcher shops and meat processors buying replacement bandsaw blades.',
    category: 'Food & Bone',
    date: '2026-06-28',
    intro:
      'Food and bone blade buyers usually need repeatable fit, clean cutting and sensible pack quantities. A complete inquiry makes reorder supply easier.',
    sections: [
      {
        heading: 'Confirm machine size',
        body:
          'Many butcher machines use similar-looking blade sizes. Send the machine model or old blade package before confirming a quotation.'
      },
      {
        heading: 'Describe the product',
        body:
          'Fresh meat, frozen meat, poultry, fish and bone sections place different demands on the tooth and weld finish. Tell the supplier what the blade will cut most often.'
      },
      {
        heading: 'Plan repeat orders',
        body:
          'If you reorder regularly, keep the SKU, size, pack quantity and destination country in one record. That makes future quotations faster.'
      }
    ],
    checklist: ['Machine model', 'Blade size', 'Fresh or frozen product', 'Bone size', 'Pack quantity', 'Destination country'],
    relatedQueries: ['bone saw blade replacement', 'butcher bandsaw blade size', 'meat cutting bandsaw blade']
  },
  {
    slug: 'bandsaw-blade-rfq-template',
    title: 'Bandsaw blade quotation request template for overseas buyers',
    description:
      'Copy this structure when asking for bandsaw blade price, MOQ, lead time and packing information.',
    category: 'Quotation template',
    date: '2026-07-02',
    intro:
      'A useful quotation request is short, specific and complete. It should let the supplier confirm fit before discussing price.',
    sections: [
      {
        heading: 'Start with the cutting job',
        body:
          'State the application first: food and bone, wood or metal. Then describe the material being cut and the machine type.'
      },
      {
        heading: 'Add the blade specification',
        body:
          'Include length, width, thickness, TPI, tooth form if known, and whether the blade must be welded to length.'
      },
      {
        heading: 'Close with commercial details',
        body:
          'Add quantity, destination country, buyer type, packaging preference and whether this is a trial order or repeat purchase.'
      }
    ],
    checklist: ['Application', 'Machine', 'Blade size', 'Material to cut', 'Quantity', 'Destination', 'Buyer type'],
    relatedQueries: ['bandsaw blade quotation request', 'band saw blade price inquiry', 'industrial blade supplier inquiry']
  },
  {
    slug: 'bi-metal-vs-carbide-bandsaw-blades',
    title: 'Bi-metal vs carbide bandsaw blades for metal cutting',
    description:
      'Compare bi-metal and carbide bandsaw blades by material difficulty, machine rigidity, cost and production demand.',
    category: 'Metal cutting',
    date: '2026-07-05',
    intro:
      'Carbide is not automatically better for every metal-cutting job. The right choice depends on material, machine condition and production volume.',
    sections: [
      {
        heading: 'When bi-metal is the practical start',
        body:
          'Bi-metal blades suit many carbon steel, alloy steel, tube, profile and mixed fabrication jobs. They are often easier to justify for general workshop use.'
      },
      {
        heading: 'When carbide deserves review',
        body:
          'Carbide can help with high-alloy, abrasive or large-section cutting when the machine is rigid enough and daily volume supports the cost.'
      },
      {
        heading: 'What to send before choosing',
        body:
          'Send material grade, section size, machine model, coolant condition and current blade life. Those details matter more than the blade label alone.'
      }
    ],
    checklist: ['Material grade', 'Section size', 'Machine rigidity', 'Coolant', 'Current blade life', 'Target output'],
    relatedQueries: ['bi metal vs carbide bandsaw blade', 'carbide tipped band saw blade', 'M42 bandsaw blade for steel']
  },
  {
    slug: 'wood-bandsaw-blade-selection-guide',
    title: 'Wood bandsaw blade selection guide for workshops',
    description:
      'Choose wood bandsaw blade width and tooth pitch for ripping, contour cutting, hardwood and general workshop use.',
    category: 'Wood cutting',
    date: '2026-07-08',
    intro:
      'Woodworking buyers should start with machine fit, workpiece thickness and cut priority before choosing a replacement blade.',
    sections: [
      {
        heading: 'Match width to the job',
        body:
          'Wider blades support straighter ripping and resawing. Narrower blades are more practical for curves and contour work when the machine allows it.'
      },
      {
        heading: 'Match pitch to chip clearance',
        body:
          'Thicker wood needs chip space. A pitch that is too fine can heat up or clog; a pitch that is too coarse can leave a rougher finish.'
      },
      {
        heading: 'Send the machine details',
        body:
          'Machine model, wheel size and old blade dimensions help confirm whether a listed blade or custom size is the better quotation path.'
      }
    ],
    checklist: ['Machine model', 'Blade size', 'Wood type', 'Thickness', 'Cut type', 'Quantity'],
    relatedQueries: ['wood bandsaw blade selection', 'resaw bandsaw blade TPI', 'hardwood band saw blade']
  },
  {
    slug: '1650mm-65-inch-meat-bandsaw-blade-guide',
    title: '1650mm and 65 inch meat bandsaw blade size guide',
    description:
      'Understand how 1650mm x 16mm x 0.56mm meat bandsaw blades map to 65 x 5/8 x .022 inch listings and 4 TPI butcher blade wording.',
    category: 'Food & Bone',
    date: '2026-07-12',
    intro:
      'The same butcher saw blade can be described as a metric size, an inch size or a compressed retail title. Knowing all three helps buyers send the correct quotation request.',
    sections: [
      {
        heading: 'Metric and inch equivalents',
        body:
          'A 1650mm blade is commonly sold as a 65 inch blade. A 16mm width is commonly written as 5/8 inch. A 0.56mm thickness is commonly written as .022 inch. Buyers may use any of these size forms.'
      },
      {
        heading: 'Common retail title pattern',
        body:
          'US-style listings often lead with the inch size, such as 65 x 5/8 x .022 x 4TPI. Metric supplier quotations often lead with 1650mm x 16mm x 0.56mm x 4TPI. Send both forms if they appear on your old blade package.'
      },
      {
        heading: 'When 4 TPI makes sense',
        body:
          'For meat and bone cutting, 4 TPI is commonly positioned for cleaner cuts and general or frozen use. For some frozen meat jobs, 3 TPI may also appear, so buyers should confirm their target finish and cutting speed.'
      }
    ],
    checklist: ['1650mm loop length', '65 inch equivalent', '16mm / 5/8 inch width', '0.56mm / .022 inch thickness', '3 or 4 TPI choice', 'Machine model'],
    relatedQueries: ['1650mm meat bandsaw blade', '65 inch meat band saw blade', '65 x 5/8 x .022 x 4TPI', 'bone saw blade 1650mm']
  }
];
