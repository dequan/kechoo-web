export type Product = {
  slug: string;
  sku: string;
  name: string;
  marketTitle: string;
  application: 'food-bone' | 'wood' | 'metal';
  technology: 'hardened' | 'bi-metal' | 'carbide';
  cutMaterial: string;
  machine: string;
  length: string;
  width: string;
  thickness: string;
  imperialSize: string;
  sizeAliases: string[];
  toothPitch: string;
  toothForm: string;
  backingMaterial: string;
  toothMaterial: string;
  recommendedRange: string;
  useCases: string[];
  compatibleMachines: string[];
  selectionNotes: string[];
  searchPhrases: string[];
  packQuantity: string;
  moq: string;
  customSize: string;
  dispatchEstimate: string;
  selectionRationale: string;
};

export const products: Product[] = [
  {
    slug: 'hardened-food-bone-blade-1650-16-056-4-tpi',
    sku: 'KH-FB-1650-16-056-4',
    name: 'Hardened Food & Bone Blade 1650 x 16 x 0.56 mm - 4 TPI',
    marketTitle: '65 x 5/8 x .022 x 4TPI Meat and Bone Band Saw Blade',
    application: 'food-bone',
    technology: 'hardened',
    cutMaterial: 'Frozen meat and bone',
    machine: 'Butcher band saws',
    length: '1650 mm',
    width: '16 mm',
    thickness: '0.56 mm',
    imperialSize: '65 in x 5/8 in x .022 in',
    sizeAliases: ['1650mm x 16mm x 0.56mm x 4TPI', '65 x 5/8 x .022 x 4TPI', '65 inch meat saw blade 4 TPI'],
    toothPitch: '4 TPI',
    toothForm: 'Regular tooth, alternate set',
    backingMaterial: 'High-carbon steel',
    toothMaterial: 'Induction-hardened high-carbon steel',
    recommendedRange: 'Fresh or frozen meat, poultry, fish and small bone sections',
    useCases: ['Butcher shop replacement blades', 'Frozen meat blocks', 'Bone-in beef, pork, lamb and fish cutting'],
    compatibleMachines: ['Most meat saws requiring a 65 inch / 1650 mm loop', 'Common J210-style butcher band saws', 'Confirm machine plate before order'],
    selectionNotes: [
      '4 TPI is commonly used when buyers want a cleaner finish than coarser 3 TPI meat blades.',
      '0.56 mm is commonly listed as .022 inch in US-style product descriptions.',
      'Send the old blade package or machine label when replacing a benchtop butcher saw blade.'
    ],
    searchPhrases: ['65 inch meat band saw blade', '65 x 5/8 x .022 x 4TPI butcher blade', '1650mm meat bandsaw blade', 'bone saw blade 65 inch 4 TPI'],
    packQuantity: '10 blades / pack',
    moq: 'Confirm by quotation',
    customSize: 'Available by technical quotation',
    dispatchEstimate: 'Confirm with sales before order',
    selectionRationale: 'Compact 65 inch replacement size for butcher and food-processing band saws; confirm machine blade length before ordering.'
  },
  {
    slug: 'hardened-food-bone-blade-1830-16-056-4-tpi',
    sku: 'KH-FB-1830-16-056-4',
    name: 'Hardened Food & Bone Blade 1830 x 16 x 0.56 mm - 4 TPI',
    marketTitle: '72 x 5/8 x .022 x 4TPI Meat and Bone Band Saw Blade',
    application: 'food-bone',
    technology: 'hardened',
    cutMaterial: 'Frozen meat and bone',
    machine: 'Butcher band saws',
    length: '1830 mm',
    width: '16 mm',
    thickness: '0.56 mm',
    imperialSize: '72 in x 5/8 in x .022 in',
    sizeAliases: ['1830mm x 16mm x 0.56mm x 4TPI', '72 x 5/8 x .022 x 4TPI', '72 inch butcher saw blade 4 TPI'],
    toothPitch: '4 TPI',
    toothForm: 'Regular tooth, alternate set',
    backingMaterial: 'High-carbon steel',
    toothMaterial: 'Induction-hardened high-carbon steel',
    recommendedRange: 'Fresh or frozen meat and medium bone sections',
    useCases: ['Medium butcher saw replacement', 'Meat processor spare blade stocking', 'Bone-in meat and frozen product cutting'],
    compatibleMachines: ['Most meat saws requiring a 72 inch / 1830 mm loop', 'Butcher and food-processing band saws', 'Confirm exact loop length before quotation'],
    selectionNotes: [
      '72 inch is a common imperial label for this metric size range.',
      'Use the machine label or old blade package to avoid confusing nearby lengths.',
      '4 TPI is a practical general/frozen-use pitch for many meat and bone jobs.'
    ],
    searchPhrases: ['72 inch butcher band saw blade', '1830mm meat bandsaw blade', '72 x 5/8 x .022 x 4TPI blade', 'meat bone saw blade 4 TPI'],
    packQuantity: '10 blades / pack',
    moq: 'Confirm by quotation',
    customSize: 'Available by technical quotation',
    dispatchEstimate: 'Confirm with sales before order',
    selectionRationale: 'Common medium butcher-saw length with a practical 4 TPI tooth pattern; verify machine compatibility.'
  },
  {
    slug: 'hardened-wood-blade-2240-16-050-4-tpi',
    sku: 'KH-WD-2240-16-050-4',
    name: 'Hardened Wood Blade 2240 x 16 x 0.50 mm - 4 TPI',
    marketTitle: '2240mm x 16mm x 0.50mm x 4TPI Wood Band Saw Blade',
    application: 'wood',
    technology: 'hardened',
    cutMaterial: 'Softwood and hardwood',
    machine: 'Woodworking band saws',
    length: '2240 mm',
    width: '16 mm',
    thickness: '0.50 mm',
    imperialSize: '88.2 in x 5/8 in x .020 in',
    sizeAliases: ['2240mm x 16mm x 0.50mm x 4TPI', '88.2 x 5/8 x .020 x 4TPI', 'wood bandsaw blade 2240mm'],
    toothPitch: '4 TPI',
    toothForm: 'Hook tooth, alternate set',
    backingMaterial: 'High-carbon steel',
    toothMaterial: 'Induction-hardened high-carbon steel',
    recommendedRange: 'General-purpose ripping and contour cutting in softwood and hardwood',
    useCases: ['General workshop replacement', 'Softwood and hardwood cutting', 'Furniture and board processing'],
    compatibleMachines: ['Woodworking band saws requiring a 2240 mm loop', 'Workshop band saws with 16 mm blade width capacity'],
    selectionNotes: [
      'Confirm whether the machine accepts 16 mm blade width before quotation.',
      '4 TPI balances chip clearance and finish for general wood cutting.',
      'For resawing thick hardwood, send workpiece thickness before choosing pitch.'
    ],
    searchPhrases: ['2240mm wood bandsaw blade', 'wood band saw blade 4 TPI', '5/8 inch wood bandsaw blade', 'hardwood bandsaw blade replacement'],
    packQuantity: '5 blades / pack',
    moq: 'Confirm by quotation',
    customSize: 'Available by technical quotation',
    dispatchEstimate: 'Confirm with sales before order',
    selectionRationale: 'General workshop specification balancing chip clearance and cut control.'
  },
  {
    slug: 'm42-bi-metal-blade-2362-19-090-6-10-tpi',
    sku: 'KB-M42-2362-19-090-6-10',
    name: 'M42 Bi-Metal Blade 2362 x 19 x 0.90 mm - 6/10 TPI',
    marketTitle: '2362mm x 19mm x 0.90mm M42 Bi-Metal Band Saw Blade 6/10 TPI',
    application: 'metal',
    technology: 'bi-metal',
    cutMaterial: 'Steel tube and profile',
    machine: 'Horizontal metal band saws',
    length: '2362 mm',
    width: '19 mm',
    thickness: '0.90 mm',
    imperialSize: '93 in x 3/4 in x .035 in',
    sizeAliases: ['2362mm x 19mm x 0.90mm x 6/10TPI', '93 x 3/4 x .035 x 6/10 TPI', 'M42 bimetal 2362mm blade'],
    toothPitch: '6/10 variable TPI',
    toothForm: 'Variable positive rake',
    backingMaterial: 'Alloy spring-steel backing',
    toothMaterial: 'M42 high-speed steel edge',
    recommendedRange: 'Thin-wall tube, profiles and small solid carbon-steel sections',
    useCases: ['Thin-wall steel tube', 'Mixed profiles', 'Small carbon-steel sections'],
    compatibleMachines: ['Horizontal metal band saws requiring a 2362 mm loop', 'Machines accepting 19 mm / 3/4 inch blade width'],
    selectionNotes: [
      '6/10 variable pitch helps reduce vibration on tube and profiles.',
      'Send wall thickness and bundle size if cutting multiple profiles together.',
      'Confirm coolant and machine condition for production cutting.'
    ],
    searchPhrases: ['M42 bi metal bandsaw blade 2362mm', '93 inch metal bandsaw blade', '6/10 TPI bandsaw blade for steel tube'],
    packQuantity: '1 welded loop',
    moq: 'Confirm by quotation',
    customSize: 'Welded-to-length service available',
    dispatchEstimate: 'Confirm with sales before order',
    selectionRationale: 'Fine variable pitch reduces vibration on thin-wall workpieces and mixed profiles.'
  },
  {
    slug: 'm42-bi-metal-blade-2450-27-090-5-8-tpi',
    sku: 'KB-M42-2450-27-090-5-8',
    name: 'M42 Bi-Metal Blade 2450 x 27 x 0.90 mm - 5/8 TPI',
    marketTitle: '2450mm x 27mm x 0.90mm M42 Bi-Metal Band Saw Blade 5/8 TPI',
    application: 'metal',
    technology: 'bi-metal',
    cutMaterial: 'Carbon steel',
    machine: 'Horizontal metal band saws',
    length: '2450 mm',
    width: '27 mm',
    thickness: '0.90 mm',
    imperialSize: '96.5 in x 1-1/16 in x .035 in',
    sizeAliases: ['2450mm x 27mm x 0.90mm x 5/8TPI', '96.5 x 1-1/16 x .035 x 5/8 TPI', 'M42 metal cutting blade 2450mm'],
    toothPitch: '5/8 variable TPI',
    toothForm: 'Variable positive rake',
    backingMaterial: 'Alloy spring-steel backing',
    toothMaterial: 'M42 high-speed steel edge',
    recommendedRange: 'Carbon-steel tube, profiles, bundles and small-to-medium solids',
    useCases: ['Fabrication shop mixed steel', 'Carbon-steel tube and profile', 'Small-to-medium solid sections'],
    compatibleMachines: ['Horizontal metal band saws requiring a 2450 mm loop', 'Machines accepting 27 mm blade width'],
    selectionNotes: [
      '5/8 variable TPI is a versatile pitch for mixed fabrication work.',
      'For thin-wall tube only, compare a finer variable pitch.',
      'For larger solids, send diameter or section size before ordering.'
    ],
    searchPhrases: ['2450mm M42 bandsaw blade', '5/8 TPI bi metal blade', 'metal cutting bandsaw blade 27mm'],
    packQuantity: '1 welded loop',
    moq: 'Confirm by quotation',
    customSize: 'Welded-to-length service available',
    dispatchEstimate: 'Confirm with sales before order',
    selectionRationale: 'Versatile variable-pitch specification for fabrication shops cutting mixed sections.'
  },
  {
    slug: 'm42-bi-metal-blade-3505-27-090-4-6-tpi',
    sku: 'KB-M42-3505-27-090-4-6',
    name: 'M42 Bi-Metal Blade 3505 x 27 x 0.90 mm - 4/6 TPI',
    marketTitle: '3505mm x 27mm x 0.90mm M42 Bi-Metal Band Saw Blade 4/6 TPI',
    application: 'metal',
    technology: 'bi-metal',
    cutMaterial: 'Carbon and alloy steel',
    machine: 'Horizontal metal band saws',
    length: '3505 mm',
    width: '27 mm',
    thickness: '0.90 mm',
    imperialSize: '138 in x 1-1/16 in x .035 in',
    sizeAliases: ['3505mm x 27mm x 0.90mm x 4/6TPI', '138 x 1-1/16 x .035 x 4/6 TPI', 'M42 bimetal 3505mm blade'],
    toothPitch: '4/6 variable TPI',
    toothForm: 'Variable positive rake',
    backingMaterial: 'Alloy spring-steel backing',
    toothMaterial: 'M42 high-speed steel edge',
    recommendedRange: 'Medium carbon- and alloy-steel solids, tube and profiles',
    useCases: ['Medium carbon steel solids', 'Alloy steel sections', 'Production tube and profile cutting'],
    compatibleMachines: ['Horizontal metal band saws requiring a 3505 mm loop', 'Industrial saws accepting 27 mm blade width'],
    selectionNotes: [
      '4/6 variable TPI provides more chip space than finer fabrication pitches.',
      'Send the largest solid section size for tooth loading review.',
      'Useful when cutting both solids and heavier profiles.'
    ],
    searchPhrases: ['3505mm M42 bandsaw blade', '4/6 TPI metal bandsaw blade', '27mm bi metal saw blade'],
    packQuantity: '1 welded loop',
    moq: 'Confirm by quotation',
    customSize: 'Welded-to-length service available',
    dispatchEstimate: 'Confirm with sales before order',
    selectionRationale: 'Production-oriented pitch for medium sections where tooth loading and chip clearance must stay balanced.'
  },
  {
    slug: 'm42-bi-metal-blade-4115-34-110-3-4-tpi',
    sku: 'KB-M42-4115-34-110-3-4',
    name: 'M42 Bi-Metal Blade 4115 x 34 x 1.10 mm - 3/4 TPI',
    marketTitle: '4115mm x 34mm x 1.10mm M42 Bi-Metal Band Saw Blade 3/4 TPI',
    application: 'metal',
    technology: 'bi-metal',
    cutMaterial: 'Large steel sections',
    machine: 'Industrial horizontal band saws',
    length: '4115 mm',
    width: '34 mm',
    thickness: '1.10 mm',
    imperialSize: '162 in x 1-3/8 in x .042 in',
    sizeAliases: ['4115mm x 34mm x 1.10mm x 3/4TPI', '162 x 1-3/8 x .042 x 3/4 TPI', 'M42 bimetal 4115mm blade'],
    toothPitch: '3/4 variable TPI',
    toothForm: 'Variable positive rake',
    backingMaterial: 'Alloy spring-steel backing',
    toothMaterial: 'M42 high-speed steel edge',
    recommendedRange: 'Medium-to-large carbon- and alloy-steel solid sections',
    useCases: ['Large carbon-steel solids', 'Alloy steel production cutting', 'Industrial horizontal saw replacement'],
    compatibleMachines: ['Industrial horizontal band saws requiring a 4115 mm loop', 'Machines accepting 34 mm / 1-3/8 inch blade width'],
    selectionNotes: [
      '3/4 variable TPI gives chip space for larger solid sections.',
      'Confirm machine rigidity and coolant when targeting blade life.',
      'If cutting thin-wall tube, request a separate pitch recommendation.'
    ],
    searchPhrases: ['4115mm M42 bandsaw blade', '3/4 TPI metal bandsaw blade', '34mm bi metal blade', '1-3/8 bandsaw blade metal cutting'],
    packQuantity: '1 welded loop',
    moq: 'Confirm by quotation',
    customSize: 'Welded-to-length service available',
    dispatchEstimate: 'Confirm with sales before order',
    selectionRationale: 'Coarser variable pitch provides chip space for larger solid sections and production cutting.'
  },
  {
    slug: 'carbide-tipped-blade-4115-34-110-2-3-tpi',
    sku: 'KC-4115-34-110-2-3',
    name: 'Carbide-Tipped Blade 4115 x 34 x 1.10 mm - 2/3 TPI',
    marketTitle: '4115mm x 34mm x 1.10mm Carbide-Tipped Band Saw Blade 2/3 TPI',
    application: 'metal',
    technology: 'carbide',
    cutMaterial: 'High-alloy and abrasive metal',
    machine: 'Rigid production band saws',
    length: '4115 mm',
    width: '34 mm',
    thickness: '1.10 mm',
    imperialSize: '162 in x 1-3/8 in x .042 in',
    sizeAliases: ['4115mm x 34mm x 1.10mm x 2/3TPI', '162 x 1-3/8 x .042 x 2/3 TPI', 'carbide tipped 4115mm blade'],
    toothPitch: '2/3 variable TPI',
    toothForm: 'Carbide-tipped positive rake',
    backingMaterial: 'High-fatigue alloy backing',
    toothMaterial: 'Carbide tips',
    recommendedRange: 'High-alloy, abrasive, difficult-to-machine and large-section metals',
    useCases: ['High-alloy steel', 'Abrasive materials', 'Large solid section cutting'],
    compatibleMachines: ['Rigid production band saws requiring a 4115 mm loop', 'Machines with stable feed and coolant control'],
    selectionNotes: [
      'Carbide should be reviewed against machine rigidity before quotation.',
      '2/3 variable TPI is for larger or more demanding sections.',
      'Send current blade life and material grade when comparing against bi-metal.'
    ],
    searchPhrases: ['4115mm carbide bandsaw blade', '2/3 TPI carbide tipped blade', 'carbide band saw blade high alloy steel'],
    packQuantity: '1 welded loop',
    moq: 'Confirm by quotation',
    customSize: 'Technical review required',
    dispatchEstimate: 'Confirm with sales before order',
    selectionRationale: 'Carbide cutting edge for rigid machines and demanding materials where bi-metal life is insufficient.'
  },
  {
    slug: 'carbide-tipped-blade-5450-41-130-2-3-tpi',
    sku: 'KC-5450-41-130-2-3',
    name: 'Carbide-Tipped Blade 5450 x 41 x 1.30 mm - 2/3 TPI',
    marketTitle: '5450mm x 41mm x 1.30mm Carbide-Tipped Band Saw Blade 2/3 TPI',
    application: 'metal',
    technology: 'carbide',
    cutMaterial: 'Large high-alloy sections',
    machine: 'Rigid production band saws',
    length: '5450 mm',
    width: '41 mm',
    thickness: '1.30 mm',
    imperialSize: '214.6 in x 1-5/8 in x .050 in',
    sizeAliases: ['5450mm x 41mm x 1.30mm x 2/3TPI', '214.6 x 1-5/8 x .050 x 2/3 TPI', 'carbide tipped 5450mm blade'],
    toothPitch: '2/3 variable TPI',
    toothForm: 'Carbide-tipped positive rake',
    backingMaterial: 'High-fatigue alloy backing',
    toothMaterial: 'Carbide tips',
    recommendedRange: 'Large high-alloy, nickel-alloy, abrasive and difficult-to-machine solid sections',
    useCases: ['Large high-alloy solid sections', 'Nickel-alloy cutting', 'Rigid production machines'],
    compatibleMachines: ['Rigid production band saws requiring a 5450 mm loop', 'Machines accepting 41 mm / 1-5/8 inch blade width'],
    selectionNotes: [
      'Best treated as a technical review item, not a blind replacement.',
      'Send material grade, section size and current blade life for comparison.',
      'Large carbide blades usually require stable machine condition and correct feed control.'
    ],
    searchPhrases: ['5450mm carbide bandsaw blade', '1-5/8 carbide tipped band saw blade', '2/3 TPI carbide blade for high alloy steel'],
    packQuantity: '1 welded loop',
    moq: 'Confirm by quotation',
    customSize: 'Technical review required',
    dispatchEstimate: 'Confirm with sales before order',
    selectionRationale: 'Heavy-duty carbide specification for stable production machines cutting large demanding sections.'
  }
];

export const getProductBySlug = (slug: string) => products.find((product) => product.slug === slug);
export const getProductsByApplication = (application: string) => products.filter((product) => product.application === application);
export const getProductsByTechnology = (technology: string) => products.filter((product) => product.technology === technology);
